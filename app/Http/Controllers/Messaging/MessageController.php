<?php

namespace App\Http\Controllers\Messaging;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\JobOffer;
use App\Http\Controllers\Controller;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Mostrar la página de mensajes
     */
    public function index()
    {
        $conversations = Conversation::where(function ($query) {
                $query->where('user_id', Auth::id())
                    ->orWhere('recipient_id', Auth::id());
            })
            ->with(['lastMessage', 'user.profile', 'user.company', 'recipient.profile', 'recipient.company', 'jobOffer'])
            ->withCount(['messages as unread_count' => function ($query) {
                $query->where('read_at', null)
                    ->where('user_id', '!=', Auth::id());
            }])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($conversation) {
                // Determinar si el usuario actual es el remitente o el destinatario
                $isUserSender = $conversation->user_id === Auth::id();
                
                return [
                    'id' => $conversation->id,
                    'with_user' => $isUserSender ? $conversation->recipient : $conversation->user,
                    'with_company' => $isUserSender 
                        ? ($conversation->recipient->company ?? null) 
                        : ($conversation->user->company ?? null),
                    'last_message' => $conversation->lastMessage,
                    'unread_count' => $conversation->unread_count,
                    'created_at' => $conversation->created_at,
                    'related_job' => $conversation->jobOffer
                ];
            });

        return Inertia::render('Messaging/Index', [
            'conversations' => $conversations
        ]);
    }

    /**
     * Obtener mensajes de una conversación
     */
    public function getMessages($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        
        // Verificar que el usuario sea parte de la conversación
        if ($conversation->user_id !== Auth::id() && $conversation->recipient_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver esta conversación');
        }
        
        // Marcar mensajes como leídos
        Message::where('conversation_id', $conversationId)
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'created_at' => $message->created_at,
                    'is_from_me' => $message->user_id === Auth::id(),
                    'read_at' => $message->read_at
                ];
            });
        
        return response()->json($messages);
    }

    /**
     * Enviar un mensaje
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        
        $conversation = Conversation::findOrFail($conversationId);
        
        // Verificar que el usuario sea parte de la conversación
        if ($conversation->user_id !== Auth::id() && $conversation->recipient_id !== Auth::id()) {
            abort(403, 'No tienes permiso para enviar mensajes en esta conversación');
        }
        
        // Verificar restricciones para alumnos
        if (Auth::user()->hasRole('alumne')) {
            // Los alumnos solo pueden responder, no iniciar conversaciones
            $hasMessages = Message::where('conversation_id', $conversationId)
                ->where('user_id', '!=', Auth::id())
                ->exists();
                
            if (!$hasMessages) {
                abort(403, 'Los alumnos no pueden iniciar conversaciones');
            }
        }
        
        $message = new Message([
            'conversation_id' => $conversationId,
            'user_id' => Auth::id(),
            'content' => $request->content
        ]);
        
        $message->save();
        
        // Actualizar timestamp de la conversación
        $conversation->touch();
        
        // Emitir evento para notificaciones en tiempo real
        broadcast(new MessageSent($message))->toOthers();
        
        return response()->json($message);
    }

    /**
     * Crear una nueva conversación (solo empresas)
     */
    public function createConversation(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
            'job_id' => 'nullable|exists:job_offers,id'
        ]);
        
        // Verificar que el usuario sea una empresa
        if (!Auth::user()->hasRole('empresa')) {
            abort(403, 'Solo las empresas pueden iniciar conversaciones');
        }
        
        // Verificar si ya existe una conversación entre estos usuarios
        $existingConversation = Conversation::where(function ($query) use ($request) {
                $query->where('user_id', Auth::id())
                    ->where('recipient_id', $request->recipient_id);
            })
            ->orWhere(function ($query) use ($request) {
                $query->where('user_id', $request->recipient_id)
                    ->where('recipient_id', Auth::id());
            })
            ->first();
        
        if ($existingConversation) {
            // Si ya existe, enviar mensaje en esa conversación
            $message = new Message([
                'conversation_id' => $existingConversation->id,
                'user_id' => Auth::id(),
                'content' => $request->message
            ]);
            
            $message->save();
            $existingConversation->touch();
            
            broadcast(new MessageSent($message))->toOthers();
            
            return response()->json([
                'conversation_id' => $existingConversation->id,
                'message' => $message
            ]);
        }
        
        // Crear nueva conversación
        $conversation = new Conversation([
            'user_id' => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'job_offer_id' => $request->job_id
        ]);
        
        $conversation->save();
        
        // Crear primer mensaje
        $message = new Message([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'content' => $request->message
        ]);
        
        $message->save();
        
        broadcast(new MessageSent($message))->toOthers();
        
        return response()->json([
            'conversation_id' => $conversation->id,
            'message' => $message
        ]);
    }
}