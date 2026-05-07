<?php

namespace App\Http\Controllers\Cliente\Autenticacion;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ControladorRecuperacionContrasena extends Controller
{
    private const TABLA_TOKENS = 'cliente_password_reset_tokens';
    private const MINUTOS_EXPIRACION = 30;

    public function mostrarSolicitud(): View
    {
        return view('cliente.autenticacion.recuperar-contrasena');
    }

    public function enviarEnlace(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'correo_electronico' => ['required', 'email'],
        ], [
            'correo_electronico.required' => 'El correo electrónico es obligatorio.',
            'correo_electronico.email' => 'Debes ingresar un correo electrónico válido.',
        ]);

        $correo = strtolower(trim($validated['correo_electronico']));
        $cliente = Cliente::where('correo_electronico', $correo)->first();

        if (! $cliente) {
            return back()
                ->withInput()
                ->with('error', 'No encontramos una cuenta registrada con ese correo electrónico.');
        }

        DB::table(self::TABLA_TOKENS)
            ->where('correo_electronico', $correo)
            ->delete();

        $tokenPlano = Str::random(64);

        DB::table(self::TABLA_TOKENS)->insert([
            'correo_electronico' => $correo,
            'token' => Hash::make($tokenPlano),
            'created_at' => now(),
        ]);

        $url = route('cliente.password.reset', [
            'token' => $tokenPlano,
            'correo' => $correo,
        ]);

        Mail::raw(
            "Hola {$cliente->nombre_completo},\n\n" .
            "Recibimos una solicitud para restablecer tu contraseña en Marly Centro de Belleza.\n\n" .
            "Ingresa al siguiente enlace para crear una nueva contraseña:\n\n" .
            "{$url}\n\n" .
            "Este enlace estará disponible durante " . self::MINUTOS_EXPIRACION . " minutos.\n\n" .
            "Si no solicitaste este cambio, puedes ignorar este mensaje.",
            function ($message) use ($correo) {
                $message->to($correo)
                    ->subject('Recuperación de contraseña | Marly Centro de Belleza');
            }
        );

        return redirect()
            ->route('cliente.ingresar')
            ->with('success', 'Te enviamos un enlace de recuperación a tu correo electrónico.');
    }

    public function mostrarRestablecer(Request $request, string $token): View|RedirectResponse
    {
        $correo = strtolower(trim((string) $request->query('correo')));

        if (! $correo || ! $this->tokenValido($correo, $token)) {
            return redirect()
                ->route('cliente.password.solicitar')
                ->with('error', 'El enlace de recuperación no es válido o ya expiró.');
        }

        return view('cliente.autenticacion.restablecer-contrasena', [
            'token' => $token,
            'correo' => $correo,
        ]);
    }

    public function actualizarContrasena(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'correo_electronico' => ['required', 'email'],
            'token' => ['required', 'string'],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'correo_electronico.required' => 'El correo electrónico es obligatorio.',
            'correo_electronico.email' => 'Debes ingresar un correo electrónico válido.',
            'token.required' => 'El token de recuperación es obligatorio.',
            'contrasena.required' => 'La nueva contraseña es obligatoria.',
            'contrasena.min' => 'La contraseña debe tener mínimo 8 caracteres.',
            'contrasena.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $correo = strtolower(trim($validated['correo_electronico']));
        $token = $validated['token'];

        if (! $this->tokenValido($correo, $token)) {
            return redirect()
                ->route('cliente.password.solicitar')
                ->with('error', 'El enlace de recuperación no es válido o ya expiró.');
        }

        $cliente = Cliente::where('correo_electronico', $correo)->first();

        if (! $cliente) {
            return redirect()
                ->route('cliente.password.solicitar')
                ->with('error', 'No encontramos una cuenta registrada con ese correo electrónico.');
        }

        $cliente->update([
            'contrasena' => Hash::make($validated['contrasena']),
        ]);

        DB::table(self::TABLA_TOKENS)
            ->where('correo_electronico', $correo)
            ->delete();

        session()->forget([
            'cliente_id',
            'cliente_nombre',
            'admin_autenticado',
            'admin_id',
            'admin_nombre',
            'admin_correo',
            'reserva_cita',
        ]);

        return redirect()
            ->route('cliente.ingresar')
            ->with('success', 'Tu contraseña fue actualizada correctamente. Ahora puedes iniciar sesión.');
    }

    private function tokenValido(string $correo, string $tokenPlano): bool
    {
        $registro = DB::table(self::TABLA_TOKENS)
            ->where('correo_electronico', $correo)
            ->latest('created_at')
            ->first();

        if (! $registro) {
            return false;
        }

        $fechaCreacion = Carbon::parse($registro->created_at);

        if ($fechaCreacion->addMinutes(self::MINUTOS_EXPIRACION)->isPast()) {
            DB::table(self::TABLA_TOKENS)
                ->where('correo_electronico', $correo)
                ->delete();

            return false;
        }

        return Hash::check($tokenPlano, $registro->token);
    }
}