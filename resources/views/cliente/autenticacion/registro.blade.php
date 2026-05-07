@extends('plantillas.app')

@section('title', 'Registro de cliente | Marly Centro de Belleza')

@section('content')
    <section class="seccion-formulario marly-registro-exacto">
        <div class="contenedor contenedor-formulario">
            <div class="tarjeta-formulario marly-registro-wrapper">

                <div class="marly-registro-header">
                    <span class="marly-registro-header-icono">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <path d="M20 8v6"></path>
                            <path d="M23 11h-6"></path>
                        </svg>
                    </span>

                    <div>
                        <h1>Registro en Marly Centro de Belleza</h1>
                        <p>Completa cada uno de tus datos para realizar el registro</p>
                    </div>
                </div>

                <a href="{{ route('cliente.google.redirigir') }}" class="marly-registro-google">
                    <img src="{{ asset('icons/google.svg') }}" alt="Google">
                    <span>Registrarme con Google</span>
                </a>

                <div class="marly-registro-divider">
                    <span>o regístrate manualmente</span>
                </div>

                <form method="POST" action="{{ route('cliente.registro.guardar') }}" class="marly-registro-form">
                    @csrf

                    <div class="marly-registro-input">
                        <span class="marly-registro-icono">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>

                        <div>
                            <label for="nombre_completo">Nombre completo</label>
                            <input type="text" id="nombre_completo" name="nombre_completo"
                                value="{{ old('nombre_completo') }}" placeholder="Ingresa tu nombre completo" required>
                        </div>
                    </div>

                    <div class="marly-registro-input">
                        <span class="marly-registro-icono">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                <path d="m3 7 9 6 9-6"></path>
                            </svg>
                        </span>

                        <div>
                            <label for="correo_electronico">Correo electrónico</label>
                            <input type="email" id="correo_electronico" name="correo_electronico"
                                value="{{ old('correo_electronico') }}" placeholder="Ingresa tu correo electrónico" required>
                        </div>
                    </div>

                    <div class="marly-registro-input">
                        <span class="marly-registro-icono">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.77.62 2.61a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.47-1.19a2 2 0 0 1 2.11-.45c.84.29 1.71.5 2.61.62A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </span>

                        <div>
                            <label for="telefono">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}"
                                placeholder="Ingresa tu número de teléfono" required>
                        </div>
                    </div>

                    <div>
                        <div class="marly-registro-input">
                            <span class="marly-registro-icono">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="5" y="11" width="14" height="10" rx="2"></rect>
                                    <path d="M8 11V8a4 4 0 1 1 8 0v3"></path>
                                </svg>
                            </span>

                            <div>
                                <label for="contrasena">Contraseña</label>
                                <input type="password" id="contrasena" name="contrasena" minlength="8"
                                    placeholder="Crea una contraseña" required>
                            </div>

                            <button type="button" class="marly-password-eye" data-password-toggle="contrasena"
                                aria-label="Mostrar contraseña">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>

                        <small class="marly-registro-ayuda">La contraseña debe tener mínimo 8 caracteres.</small>
                    </div>

                    <div class="marly-registro-input">
                        <span class="marly-registro-icono">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="5" y="11" width="14" height="10" rx="2"></rect>
                                <path d="M8 11V8a4 4 0 1 1 8 0v3"></path>
                            </svg>
                        </span>

                        <div>
                            <label for="contrasena_confirmation">Confirmar contraseña</label>
                            <input type="password" id="contrasena_confirmation" name="contrasena_confirmation"
                                minlength="8" placeholder="Confirma tu contraseña" required>
                        </div>

                        <button type="button" class="marly-password-eye" data-password-toggle="contrasena_confirmation"
                            aria-label="Mostrar contraseña">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>

                    <div class="marly-registro-acciones">
                        <button type="submit" class="marly-registro-submit">Registrarme</button>
                        <a href="{{ route('cliente.ingresar') }}" class="marly-registro-login">Ya tengo cuenta</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection