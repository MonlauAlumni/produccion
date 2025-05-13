<?php

namespace App\Http\Controllers\Messaging;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\JobOffer;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * Mostrar la vista principal de mensajes
     */
    public function index()
    {
       $company = Auth::user()->company;

       $job_offers = $company->jobOffers()->where('status', 'active')->get();

        return Inertia::render('Messaging/Index', [
            'companyJobs' => $job_offers,
        ]);
    }
    
    /**
     * Obtener todas las conversaciones del usuario autenticado
     */
    public function getConversations()
    {
        $user = Auth::user();

        // Obtener las conversaciones
        $conversations = Conversation::where('user_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->with(['lastMessage', 'user', 'recipient', 'jobOffer'])
            ->withCount(['messages as unread_count' => function($query) use ($user) {
                $query->where('user_id', '!=', $user->id)
                    ->whereNull('read_at');
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Transformar las conversaciones para tener el formato correcto
        $conversations->transform(function($conversation) use ($user) {
            // Determinar el participante (el otro usuario)
            $participant = $conversation->user_id === $user->id 
                ? $conversation->recipient 
                : $conversation->user;
                
            return [
                'id' => $conversation->id,
                'participant' => $participant,
                'last_message' => $conversation->lastMessage ? $conversation->lastMessage->content : null, // Cambiado a content
                'updated_at' => $conversation->updated_at,
                'unread_count' => $conversation->unread_count,
                'job' => $conversation->jobOffer
            ];
        });
        
        return response()->json([
            'conversations' => $conversations,
            'currentUser' => $user
        ]);
    }
    
    /**
     * Obtener mensajes de una conversación específica
     */
    public function getMessages(Conversation $conversation)
    {
        $user = Auth::user();
        
        // Verificar que el usuario sea parte de la conversación
        if ($conversation->user_id !== $user->id && $conversation->recipient_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        
        // Obtener mensajes paginados
        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        // Transformar los mensajes para añadir is_sender
        $messages->getCollection()->transform(function($message) use ($user) {
            $message->is_sender = $message->user_id === $user->id;
            // Asegurar que message tenga la propiedad message para compatibilidad
            $message->message = $message->content;
            return $message;
        });
        
        return response()->json([
            'messages' => $messages
        ]);
    }
    
    /**
     * Enviar un mensaje en una conversación existente
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {

        $user = Auth::user();
      
        // Verificar que el usuario sea parte de la conversación
        if ($conversation->user_id !== $user->id && $conversation->recipient_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        
        // Validar el mensaje
        $request->validate([
            'message' => 'required|string|max:1000' // Cambiado de content a message
        ]);
        
        // Crear el mensaje
        $message = $conversation->messages()->create([
            'user_id' => $user->id,
            'content' => $request->message, // Usar message del request pero guardar como content
        ]);
      
        // Actualizar la fecha de la conversación
        $conversation->touch();
 
        // Cargar la relación de usuario
        $message->load('user');
        
        // Añadir is_sender y message para compatibilidad
        $message->is_sender = true;
        $message->message = $message->content;

        // Determinar el destinatario del mensaje
        $recipientId = $conversation->user_id === $user->id 
            ? $conversation->recipient_id 
            : $conversation->user_id;
            
        // Solo enviar notificación si el destinatario no es el remitente
        if ($recipientId !== $user->id) {
            app(\App\Http\Controllers\Notifications\NotificationController::class)
                ->sendNotification(
                    $recipientId, 
                    'message', 
                    'Tienes un nuevo mensaje de ' . $user->name
                );
        }

        // Emitir evento para notificaciones en tiempo real
        broadcast(new MessageSent($message))->toOthers();

        // Crear notificación en la base de datos

        
        // Emitir evento para WebSockets
        $recipientId = $conversation->user_id === $user->id 
            ? $conversation->recipient_id 
            : $conversation->user_id;
            
        broadcast(new MessageSent($message, $recipientId))->toOthers();
        
        return response()->json([
            'message' => $message
        ]);
    }
    
    /**
     * Crear una nueva conversación
     */
    public function createConversation(Request $request)
    {
        $user = Auth::user();
        
        // Registrar los datos recibidos para depuración
        Log::info('Datos recibidos para crear conversación:', $request->all());
        
        // Validar la solicitud
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
            'job_id' => 'nullable|exists:job_offers,id'
        ]);
        
        // Verificar si ya existe una conversación entre estos usuarios
        $existingConversation = Conversation::where(function($query) use ($user, $request) {
            $query->where('user_id', $user->id)
                  ->where('recipient_id', $request->recipient_id);
        })->orWhere(function($query) use ($user, $request) {
            $query->where('user_id', $request->recipient_id)
                  ->where('recipient_id', $user->id);
        })->first();
        
        if ($existingConversation) {
            // Si existe, enviar mensaje en esa conversación
            $message = $existingConversation->messages()->create([
                'user_id' => $user->id,
                'content' => $validated['message']
            ]);
            
            // Actualizar la conversación
            $existingConversation->touch();
            
            // Si se proporciona un job_id, actualizar la conversación
            if ($request->job_id) {
                $existingConversation->update(['job_id' => $validated['job_id']]);
            }
            
            // Cargar relaciones
            $message->load('user');
            
            // Determinar el participante (el otro usuario)
            $participant = $existingConversation->user_id === $user->id 
                ? $existingConversation->recipient 
                : $existingConversation->user;
            
            // Añadir message para compatibilidad
            $message->message = $message->content;
            
            // Emitir evento
            broadcast(new MessageSent($message, $request->recipient_id))->toOthers();
            
            return response()->json([
                'conversation' => [
                    'id' => $existingConversation->id,
                    'participant' => $participant,
                    'last_message' => $validated['message'],
                    'updated_at' => now(),
                    'unread_count' => 0,
                    'job' => $existingConversation->jobOffer
                ],
                'message' => $message
            ]);
        }
        
        // Crear nueva conversación
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'recipient_id' => $validated['recipient_id'],
            'job_id' => $validated['job_id']
        ]);
        
        // Crear el primer mensaje
        $message = $conversation->messages()->create([
            'user_id' => $user->id,
            'content' => $validated['message']
        ]);
        
        // Cargar relaciones
        $message->load('user');
        $recipient = User::find($validated['recipient_id']);
        
        // Añadir message para compatibilidad
        $message->message = $message->content;
        
        // Emitir evento
        broadcast(new MessageSent($message, $validated['recipient_id']))->toOthers();
        
        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'participant' => $recipient,
                'last_message' => $validated['message'],
                'updated_at' => now(),
                'unread_count' => 0,
                'job' => $conversation->jobOffer
            ],
            'message' => $message
        ]);
    }
    
    /**
     * Marcar una conversación como leída
     */
    public function markAsRead(Conversation $conversation)
    {
        $user = Auth::user();
        
        // Verificar que el usuario sea parte de la conversación
        if ($conversation->user_id !== $user->id && $conversation->recipient_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        
        // Marcar todos los mensajes no leídos como leídos
        $conversation->messages()
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return response()->json(['success' => true]);
    }
    
    /**
     * Buscar alumni para el modal de nueva conversación
     */
   public function searchAlumni(Request $request)
    {
        $query = $request->get('q', '');
        
        $alumni = User::whereHas('roles', function ($query) {
            $query->where('name', 'alumne');
        })
        ->with('profile')
        ->orderBy('name', 'asc')
        ->limit(500) // Aumentamos el límite para cargar más alumni de una vez
        ->get();
     
        return response()->json(['alumni' => $alumni]);
    }
    
    /**
     * Obtener trabajos de la empresa para el modal de nueva conversación
     */
    public function getCompanyJobs()
    {
        $user = Auth::user();
        
        // Verificar que el usuario sea una empresa
        if (!$user->hasRole('empresa')) { // Ajustar según tu sistema de roles
            return response()->json(['jobs' => []]);
        }
        
        // Ajustar según tu estructura de datos
        $jobs = $user->company->jobs()
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json(['jobs' => $jobs]);
    }
}