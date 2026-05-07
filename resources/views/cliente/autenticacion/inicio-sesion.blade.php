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

                <a href="{{ route('cliente.google.redirigir') }}" class="marly-login-google">
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