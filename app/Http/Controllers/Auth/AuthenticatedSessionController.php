<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * AuthenticatedSessionController — inicio y cierre de sesión (Breeze).
 *
 * - create()/store(): muestra el login, autentica y REGENERA la sesión
 *   (session()->regenerate()) para prevenir la fijación de sesión.
 *   Tras entrar, redirige al panel según el rol (encargado o estudiante).
 * - destroy(): cierra la sesión, la invalida y regenera el token CSRF.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirige al panel que corresponde según el rol del usuario.
        $panel = $request->user()->rol === 'encargado'
            ? 'encargado.dashboard'
            : 'estudiante.dashboard';

        return redirect()->intended(route($panel, absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
