<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarSesionAdministradora
{
    private const ADMIN_EMAIL = 'admin@marly.com';

    public function handle(Request $request, Closure $next): Response
    {
        if (! session('admin_autenticado') || session('admin_correo') !== self::ADMIN_EMAIL) {
            return redirect()->route('cliente.ingresar')->with('error', 'Debes iniciar sesión como administradora para acceder al panel.');
        }

        return $next($request);
    }
}
