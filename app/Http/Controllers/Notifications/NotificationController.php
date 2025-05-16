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

        if (Notification::where('user_id', $userId)->count() > 50) {
            Notification::where('user_id', $userId)->orderBy('created_at', 'asc')->first()->delete();
        }
    }

    public function update(Request $request, $notificationId)
    {
        $notification = Notification::find($notificationId);

        if ($notification && $notification->user_id === $request->user()->id) {
            $notification->is_read = true;
            $notification->save();
        }

        return redirect()->back();
    }

    public function updateAll(Request $request)
    {
        $userId = $request->user()->id;

        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back();
    }

    public function destroy(Request $request, $notificationId)
    {
        $notification = Notification::find($notificationId);

        if ($notification && $notification->user_id === $request->user()->id) {
            $notification->delete();
        }

        return redirect()->back();
    }

    public function destroyAll(Request $request)
    {
        $userId = $request->user()->id;

        Notification::where('user_id', $userId)->delete();

        return redirect()->back();
    }

}
