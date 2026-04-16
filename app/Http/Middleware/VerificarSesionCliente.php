<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarSesionCliente
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('cliente_id')) {
            return redirect()->route('cliente.ingresar')->with('error', 'Debes iniciar sesión para continuar.');
        }

        return $next($request);
    }
}
