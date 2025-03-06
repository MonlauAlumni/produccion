<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;


class SettingsController extends Controller
{
    public function show() {
        return Inertia::render('Settings/Settings');
    }

    public function changePassword() {
        return Inertia::render('ChangePassword');
    }

    public function updateUser(Request $request)
{
    $user = auth()->user();
    
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        'allow_emails' => ['boolean'],
    ]);
    
    $user->name = $validated['name'];
    
    if ($user->email !== $validated['email']) {
        $user->email = $validated['email'];
        // $user->email_verified_at = null;
    }
    
    // if (isset($validated['allow_emails'])) {
    //     $user->allow_emails = $validated['allow_emails'];
    // }
    
    $user->save();
    
    if ($request->wantsJson()) {
        return response()->json([
            'message' => 'User information updated successfully',
            'user' => $user
        ]);
    }
    
    return back()->with('success', 'User information updated successfully');
}

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);
    
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
                'errors' => [
                    'current_password' => ['The provided password does not match your current password.']
                ]
            ], 422);
        }
    
        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();
    
        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }
}