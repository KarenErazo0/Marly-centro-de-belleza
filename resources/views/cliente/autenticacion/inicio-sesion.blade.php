@extends('plantillas.app')

@section('title', 'Inicio de sesión | Marly Centro de Belleza')

@section('content')
    <section class="marly-login-exacto">
        <div class="contenedor">
            <div class="marly-login-wrapper">

                <div class="marly-login-header">
                    <h1>Iniciar sesión</h1>
                    <p>Accede con tu correo electrónico y tu contraseña.</p>
                </div>

                <svg class="google-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="28" height="28">
    <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.6 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.1 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.4-.4-3.5z"/>
    <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.1 6.1 29.3 4 24 4 16.3 4 9.6 8.3 6.3 14.7z"/>
    <path fill="#4CAF50" d="M24 44c5.1 0 9.8-1.9 13.3-5.1l-6.2-5.2C29.1 35.2 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-7.9l-6.5 5C9.5 39.6 16.2 44 24 44z"/>
    <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.2-2.2 4.1-4.2 5.7l6.2 5.2C36.9 39.2 44 34 44 24c0-1.3-.1-2.4-.4-3.5z"/>
</svg>
                    <img src="{{ asset('icons/google.svg') }}" alt="Google">
                    <span>Continuar con Google</span>
                </a>

                <div class="marly-login-divider">
                    <span>o</span>
                </div>

                <form method="POST" action="{{ route('cliente.ingresar.validar') }}" class="marly-login-form">
                    @csrf

                    <div class="marly-login-group">
                        <label for="correo_electronico">Correo electrónico</label>

                        <div class="marly-login-input">
                            <span class="marly-login-icono">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z"/>
                                </svg>
                            </span>

                            <input
                                type="email"
                                id="correo_electronico"
                                name="correo_electronico"
                                value="{{ old('correo_electronico') }}"
                                placeholder="tu@email.com"
                                required>
                        </div>
                    </div>

                    <div class="marly-login-group">
                        <label for="contrasena">Contraseña</label>

                        <div class="marly-login-input">
                            <span class="marly-login-icono">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="5" y="11" width="14" height="10" rx="2"></rect>
                                    <path d="M8 11V8a4 4 0 1 1 8 0v3"></path>
                                </svg>
                            </span>

                            <input
                                type="password"
                                id="contrasena"
                                name="contrasena"
                                placeholder="••••••••"
                                required>

                            <button type="button" class="marly-login-eye" aria-label="Mostrar contraseña">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>

                        <div class="marly-login-forgot-row">
                            <a href="{{ route('cliente.password.solicitar') }}">¿Olvidaste tu contraseña?</a>
                        </div>
                    </div>

                    <button type="submit" class="marly-login-submit">
                        Iniciar sesión
                    </button>

                    <p class="marly-login-register">
                        ¿No tienes cuenta?
                        <a href="{{ route('cliente.registro') }}">Crear cuenta</a>
                    </p>
                </form>
            </div>
        </div>
    </section>

@endsection