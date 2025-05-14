<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotFoundController extends Controller
{
    // Redirige a la página de inicio
    public function redirectToHome()
    {
        return redirect()->route('home');
    }

    // Método para manejar la página no encontrada en Laravel
    public function show404()
    {
        // Si necesitas renderizar una vista de error personalizada en Laravel
        return view('errors.404');
    }
}
