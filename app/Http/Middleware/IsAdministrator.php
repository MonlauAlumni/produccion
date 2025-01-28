<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdministrator
{
    public function handle(Request $request, Closure $next)
    {
        // Comprueba si el usuario tiene el rol de administrador
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Redirige o lanza error si no tiene permiso
        abort(403, 'Unauthorized');
    }
}
