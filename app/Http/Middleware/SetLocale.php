<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && $request->user()->settings()->exists()) {
            $locale = auth()->user()->settings()->first()->language ?? config('app.locale');
            App::setLocale($locale);
        }

        return $next($request);
    }
}