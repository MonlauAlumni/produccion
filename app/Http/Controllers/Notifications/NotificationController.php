<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function show(Request $request)
    {
        $userNotifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Transformar a formato paginado que espera el componente
        return Inertia::render('Notifications/Notifications', [
            'notifications' => [
                'data' => $userNotifications,
                'current_page' => 1,
                'last_page' => 1,
                'from' => 1,
                'to' => count($userNotifications),
                'total' => count($userNotifications)
            ],
            'unreadCount' => $userNotifications->where('is_read', false)->count()
        ]);
    }

    public function sendNotification($userId, $type, $message)
    {
        Log::info("Enviando notificación a user $userId de tipo $type con mensaje: $message");

        $notification = new Notification();
        $notification->user_id = $userId;
        $notification->type = $type;
        $notification->message = $message;
        $notification->is_read = false;
        $notification->save();
    }

}
