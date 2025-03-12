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
        return Inertia::render('Settings/ChangePassword');
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
    $request->validate([
        'current_password' => ['required', 'current_password'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'password_confirmation' => ['required'],
    ], [
        'current_password.required' => 'La contraseña actual es obligatoria.',
        'current_password.current_password' => 'La contraseña actual no es correcta.',
        'password.required' => 'La nueva contraseña es obligatoria.',
        'password.string' => 'La nueva contraseña debe ser una cadena de texto.',
        'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
        'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        'password_confirmation.required' => 'La confirmación de la nueva contraseña es obligatoria.',
    ]);

    $user = $request->user();
    $user->password = Hash::make($request->password);
    $user->save();

    return response()->json([
        'message' => 'Contraseña actualizada correctamente'
    ]);
}
}