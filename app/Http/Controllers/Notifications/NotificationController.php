<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function show(Request $request)
    {
        return Inertia::render('Notifications/Notifications', [
        ]);

    }
}
