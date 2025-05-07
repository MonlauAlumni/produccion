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
        return Inertia::render('Notifications/Notifications', [
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
