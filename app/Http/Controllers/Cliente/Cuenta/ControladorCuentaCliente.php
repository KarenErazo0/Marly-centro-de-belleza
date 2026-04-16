<?php

namespace App\Http\Controllers\Cliente\Cuenta;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ControladorCuentaCliente extends Controller
{
    public function index(): View
    {
        $cliente = Cliente::findOrFail(session('cliente_id'));

        return view('cliente.cuenta.panel', compact('cliente'));
    }

    public function update(Request $request): RedirectResponse
    {
        $cliente = Cliente::findOrFail(session('cliente_id'));

        $validated = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:100'],
            'correo_electronico' => ['required', 'email', 'max:100', 'unique:clientes,correo_electronico,' . $cliente->id_cliente . ',id_cliente'],
            'telefono' => ['required', 'string', 'max:20'],
            'contrasena' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'correo_electronico.required' => 'El correo electrónico es obligatorio.',
            'correo_electronico.email' => 'Debes ingresar un correo electrónico válido.',
            'correo_electronico.unique' => 'Este correo electrónico ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'contrasena.min' => 'La contraseña debe tener mínimo 8 caracteres.',
            'contrasena.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $data = [
            'nombre_completo' => $validated['nombre_completo'],
            'correo_electronico' => $validated['correo_electronico'],
            'telefono' => $validated['telefono'],
        ];

        if (!empty($validated['contrasena'])) {
            $data['contrasena'] = Hash::make($validated['contrasena']);
        }

        $cliente->update($data);

        session()->put('cliente_nombre', $cliente->nombre_completo);

        return back()->with('success', 'Los datos de tu cuenta fueron actualizados correctamente.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'confirmar_eliminacion' => ['required', 'accepted'],
        ], [
            'confirmar_eliminacion.required' => 'Debes confirmar la eliminación de la cuenta.',
            'confirmar_eliminacion.accepted' => 'Debes confirmar la eliminación de la cuenta.',
        ]);

        $cliente = Cliente::findOrFail(session('cliente_id'));
        $cliente->delete();

        session()->forget(['cliente_id', 'cliente_nombre']);

        return redirect()->route('inicio')->with('success', 'Tu cuenta fue eliminada correctamente.');
    }
}
