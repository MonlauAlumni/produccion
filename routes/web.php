<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return Inertia::render('Home'); 
});

// Ruta para mostrar el formulario de registro
Route::get('/register', [RegisterController::class, 'create'])->name('register');

// Ruta para manejar el envío del formulario de registro
Route::post('/register', [RegisterController::class, 'store']);
