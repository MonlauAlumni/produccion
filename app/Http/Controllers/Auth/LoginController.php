<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    public function show(Request $request)
    {
        return Inertia::render('Auth/MonlauLogin');
    }

    public function store(Request $request)
    {
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Si la validación falla, se devuelven los errores
        if ($validator->fails()) {
            return Inertia::render('Auth/Login', [
                'errors' => $validator->errors(),
            ]);
        }

        // Buscar al usuario por correo electrónico
        $user = User::where('email', $request->email)->first();

        // Comprobar si el usuario existe y si la contraseña es correcta
        if (!$user || !Hash::check($request->password, $user->password)) {
            return Inertia::render('Auth/Login', [
                'errors' => 'The provided credentials do not match our records.',
            ]);
        }

        // Si el usuario tiene habilitada la autenticación de dos factores
        if ($user->two_factor_secret) {
            // Guardamos el ID del usuario en la sesión para usarlo en el flujo de 2FA
            $request->session()->put('login.id', $user->id);
            $request->session()->save();

            // Redirigir al flujo de 2FA
            return redirect()->route('two-factor.login');
        }

        // Si no tiene habilitado 2FA, iniciamos sesión directamente
        Auth::login($user, $request->has('remember'));
        $request->session()->regenerate();

        if (!$user->settings()->exists()) {
            $user->settings()->create([]);
        }

        return redirect()->route('home');
    }

    public function destroy(Request $request)
    {
        // Cerrar sesión
        Auth::logout();

        // Invalidar la sesión
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
