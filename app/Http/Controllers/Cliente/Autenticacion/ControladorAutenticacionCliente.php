<?php

namespace App\Http\Controllers\Cliente\Autenticacion;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ControladorAutenticacionCliente extends Controller
{
    public function mostrarRegistro(): View
    {
        return view('cliente.autenticacion.registro');
    }

    public function registrar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:100'],
            'correo_electronico' => ['required', 'email', 'max:100', 'unique:clientes,correo_electronico'],
            'telefono' => ['required', 'string', 'max:20'],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'correo_electronico.required' => 'El correo electrónico es obligatorio.',
            'correo_electronico.email' => 'Debes ingresar un correo electrónico válido.',
            'correo_electronico.unique' => 'Este correo electrónico ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.min' => 'La contraseña debe tener mínimo 8 caracteres.',
            'contrasena.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $cliente = Cliente::create([
            'nombre_completo' => $validated['nombre_completo'],
            'correo_electronico' => $validated['correo_electronico'],
            'telefono' => $validated['telefono'],
            'contrasena' => Hash::make($validated['contrasena']),
            'fecha_registro' => now(),
        ]);

        session()->put([
            'cliente_id' => $cliente->id_cliente,
            'cliente_nombre' => $cliente->nombre_completo,
        ]);

        return redirect()->route('cliente.cuenta')->with('success', 'Registro exitoso. Tu cuenta fue creada correctamente.');
    }

    public function mostrarIngreso(): View
    {
        return view('cliente.autenticacion.inicio-sesion');
    }

    public function ingresar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'correo_electronico' => ['required', 'email'],
            'contrasena' => ['required', 'string'],
        ], [
            'correo_electronico.required' => 'El correo electrónico es obligatorio.',
            'correo_electronico.email' => 'Debes ingresar un correo electrónico válido.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ]);

        $cliente = Cliente::where('correo_electronico', $validated['correo_electronico'])->first();

        if (! $cliente || ! Hash::check($validated['contrasena'], $cliente->contrasena)) {
            return back()->withInput()->with('error', 'Correo o contraseña incorrectos.');
        }

        session()->put([
            'cliente_id' => $cliente->id_cliente,
            'cliente_nombre' => $cliente->nombre_completo,
        ]);

        return redirect()->route('cliente.cuenta')->with('success', 'Bienvenida de nuevo.');
    }

    public function salir(): RedirectResponse
    {
        session()->forget(['cliente_id', 'cliente_nombre']);

        return redirect()->route('inicio')->with('success', 'Sesión cerrada correctamente.');
    }
}
