<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Verifica que el usuario autenticado tenga el rol requerido.
     *
     * - Si no hay sesión, redirige al login con un mensaje claro.
     * - Si el rol no corresponde, redirige al panel del rol que sí tiene.
     */
    public function handle(Request $request, Closure $next, string $rol): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Debe iniciar sesión para acceder.',
            ]);
        }

        if (Auth::user()->rol !== $rol) {
            // El usuario intentó entrar a un panel que no le corresponde.
            return Auth::user()->rol === 'encargado'
                ? redirect()->route('encargado.dashboard')
                : redirect()->route('estudiante.dashboard');
        }

        return $next($request);
    }
}
