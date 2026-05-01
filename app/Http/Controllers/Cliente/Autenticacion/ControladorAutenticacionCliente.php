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
    private const ADMIN_EMAIL = 'admin@marly.com';
    private const ADMIN_PASSWORD = 'admin12345';
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

        $correo = strtolower(trim($validated['correo_electronico']));

        if ($correo === self::ADMIN_EMAIL) {
            $administradora = Cliente::firstOrCreate(
                ['correo_electronico' => self::ADMIN_EMAIL],
                [
                    'nombre_completo' => 'Administradora Marly',
                    'telefono' => '3150000000',
                    'contrasena' => Hash::make(self::ADMIN_PASSWORD),
                    'fecha_registro' => now(),
                ]
            );

            if (! Hash::check($validated['contrasena'], $administradora->contrasena)) {
                return back()->withInput()->with('error', 'Correo o contraseña incorrectos.');
            }

            session()->forget(['cliente_id', 'cliente_nombre', 'reserva_cita']);
            session()->put([
                'admin_autenticado' => true,
                'admin_id' => $administradora->id_cliente,
                'admin_nombre' => $administradora->nombre_completo,
                'admin_correo' => $administradora->correo_electronico,
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Bienvenida al panel de administración.');
        }

        $cliente = Cliente::where('correo_electronico', $correo)->first();

        if (! $cliente || ! Hash::check($validated['contrasena'], $cliente->contrasena)) {
            return back()->withInput()->with('error', 'Correo o contraseña incorrectos.');
        }

        session()->forget(['admin_autenticado', 'admin_id', 'admin_nombre', 'admin_correo']);
        session()->put([
            'cliente_id' => $cliente->id_cliente,
            'cliente_nombre' => $cliente->nombre_completo,
        ]);

        return redirect()->route('cliente.cuenta')->with('success', 'Bienvenida de nuevo.');
    }

    public function salir(): RedirectResponse
    {
        session()->forget(['cliente_id', 'cliente_nombre', 'admin_autenticado', 'admin_id', 'admin_nombre', 'admin_correo', 'reserva_cita']);

        return redirect()->route('inicio')->with('success', 'Sesión cerrada correctamente.');
    }
}
