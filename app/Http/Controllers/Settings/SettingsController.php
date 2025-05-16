<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function show(Request $request)
    {
        $settings = $request->user()->settings()->first();
        return Inertia::render('Settings/Settings', [
            'settings' => $request->user()->settings()->first(),
            'profile' => $request->user()->profile,

        ]);
    }

    public function changePassword()
    {
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
        if ($user->name !== $validated['name']) {
            $user->name = $validated['name'];

        }

        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];
        }

        $user->save();

        $profile = $user->profile;
        $slang = strtolower(
            str_replace(' ', '-', trim("{$user->name}-{$user->last_name_1}" . ($user->last_name_2 ? "-{$user->last_name_2}" : "")))
        );
        $profile->slang = $slang;
        $profile->save();
        return redirect()->route('settings.show')->with('success', 'Apariencia actualizada correctamente');

    }

    public function changeLanguage(Request $request)
    {
        $validated = $request->validate([
            'language' => ['required', 'string'],
        ]);

        $user = $request->user();
        $settings = $user->settings()->first();
        $settings->language = $validated['language'];
        $settings->save();

        return redirect()->route('settings.show')->with('success', 'Apariencia actualizada correctamente');
    }

    public function updateAppearance(Request $request)
    {
        $validated = $request->validate([
            'highlight_color' => ['required', 'string'],
            'font_size' => ['required', 'integer'],
        ]);

        $user = $request->user();
        $settings = $user->settings()->first();
        $settings->highlight_color = $validated['highlight_color'];
        $settings->font_size = $validated['font_size'];
        $settings->save();

        return redirect()->route('settings.show')->with('success', 'Apariencia actualizada correctamente');
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

        return redirect()->route('settings.show')->with('success', 'Apariencia actualizada correctamente');

    }
}