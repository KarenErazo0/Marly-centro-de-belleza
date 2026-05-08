@extends('plantillas.app')

@section('title', 'Mi cuenta | Marly Centro de Belleza')

@section('content')
    <section class="seccion-cuenta marly-cuenta-exacto">
        <div class="contenedor marly-cuenta-contenedor">

            <div class="marly-cuenta-banner marly-cuenta-banner-temporal" data-alerta-temporal>
                <div class="marly-cuenta-banner-icono">✓</div>
                <div>
                    <h2>¡Bienvenido de nuevo, {{ $cliente->nombre_completo }}!</h2>
                    <p>Gracias por confiar en Marly Centro de Belleza.</p>
                </div>
                <div class="marly-cuenta-flor" aria-hidden="true">✦</div>
            </div>

            <div class="tarjeta-cuenta bienvenida-cuenta cuenta-principal marly-cuenta-card">
                <div class="marly-cuenta-header">
                    <div class="marly-cuenta-avatar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21a8 8 0 0 0-16 0"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>

                    <div>
                        <h1>Hola, {{ $cliente->nombre_completo }}</h1>
                        <p>Desde aquí puedes gestionar tus citas, editar tus datos personales o cerrar sesión.</p>
                    </div>
                </div>

                <div class="acciones-panel-cliente marly-cuenta-acciones">
                    <a href="{{ route('cliente.citas.index') }}" class="marly-cuenta-opcion marly-cuenta-opcion-activa">
                        <span class="marly-cuenta-opcion-icono">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="17" rx="2"></rect>
                                <path d="M16 2v4"></path>
                                <path d="M8 2v4"></path>
                                <path d="M3 10h18"></path>
                            </svg>
                        </span>
                        <span>
                            <strong>Ver mis citas</strong>
                            <small>Consulta y administra todas tus citas</small>
                        </span>
                        <b>›</b>
                    </a>

                    <a href="{{ route('cliente.citas.agendar.servicios') }}" class="marly-cuenta-opcion">
                        <span class="marly-cuenta-opcion-icono">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="17" rx="2"></rect>
                                <path d="M16 2v4"></path>
                                <path d="M8 2v4"></path>
                                <path d="M3 10h18"></path>
                                <path d="M12 14v4"></path>
                                <path d="M10 16h4"></path>
                            </svg>
                        </span>
                        <span>
                            <strong>Agendar nueva cita</strong>
                            <small>Reserva un nuevo servicio en pocos pasos</small>
                        </span>
                        <b>›</b>
                    </a>

                    <button type="button" class="marly-cuenta-opcion" id="btn-editar-cuenta">
                        <span class="marly-cuenta-opcion-icono">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21a8 8 0 0 0-16 0"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                        <span>
                            <strong>Editar datos personales</strong>
                            <small>Actualiza tu información personal y de contacto</small>
                        </span>
                        <b>›</b>
                    </button>
                </div>

                <div class="marly-cuenta-separador"></div>

                <form action="{{ route('cliente.salir') }}" method="POST"
                    class="form-cerrar-sesion-cuenta marly-cuenta-salir-form">
                    @csrf
                    <button type="submit" class="marly-cuenta-salir">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <path d="M10 17l5-5-5-5"></path>
                            <path d="M15 12H3"></path>
                        </svg>
                        Cerrar sesión
                    </button>
                </form>
            </div>

            <div class="tarjeta-cuenta panel-edicion-cuenta marly-cuenta-card marly-panel-edicion-premium"
                id="panel-edicion-cuenta">
                <div class="marly-edicion-header">
                    <h2>Editar datos personales</h2>
                    <p>Actualiza tu información personal y de contacto.</p>
                </div>

                <form method="POST" action="{{ route('cliente.cuenta.actualizar') }}" class="marly-edicion-form">
                    @csrf
                    @method('PUT')

                    <div class="marly-edicion-fila">
                        <div class="marly-edicion-info">
                            <span class="marly-edicion-icono">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M20 21a8 8 0 0 0-16 0"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <div>
                                <label for="nombre_completo">Nombre completo</label>
                                <small>Ingresa tu nombre y apellido.</small>
                            </div>
                        </div>

                        <input type="text" id="nombre_completo" name="nombre_completo"
                            value="{{ old('nombre_completo', $cliente->nombre_completo) }}" required>
                    </div>

                    <div class="marly-edicion-fila">
                        <div class="marly-edicion-info">
                            <span class="marly-edicion-icono">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 4h16v16H4z"></path>
                                    <path d="m22 6-10 7L2 6"></path>
                                </svg>
                            </span>
                            <div>
                                <label for="correo_electronico">Correo electrónico</label>
                                <small>Este será tu usuario para iniciar sesión.</small>
                            </div>
                        </div>

                        <input type="email" id="correo_electronico" name="correo_electronico"
                            value="{{ old('correo_electronico', $cliente->correo_electronico) }}" required>
                    </div>

                    <div class="marly-edicion-fila">
                        <div class="marly-edicion-info">
                            <span class="marly-edicion-icono">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path
                                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.32 1.77.59 2.61a2 2 0 0 1-.45 2.11L8 9.69a16 16 0 0 0 6.31 6.31l1.25-1.25a2 2 0 0 1 2.11-.45c.84.27 1.71.47 2.61.59A2 2 0 0 1 22 16.92z">
                                    </path>
                                </svg>
                            </span>
                            <div>
                                <label for="telefono">Teléfono</label>
                                <small>Ingresa tu número de teléfono de contacto.</small>
                            </div>
                        </div>

                        <input type="text" id="telefono" name="telefono"
                            value="{{ old('telefono', $cliente->telefono) }}" required>
                    </div>

                    <div class="marly-edicion-fila">
                        <div class="marly-edicion-info">
                            <span class="marly-edicion-icono">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="5" y="11" width="14" height="10" rx="2"></rect>
                                    <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
                                </svg>
                            </span>
                            <div>
                                <label for="contrasena">Nueva contraseña (opcional)</label>
                                <small>Deja en blanco si no deseas cambiar tu contraseña.</small>
                            </div>
                        </div>

                        <div class="marly-edicion-password">
                            <input type="password" id="contrasena" name="contrasena" minlength="8"
                                placeholder="Ingresa tu nueva contraseña">
                            <button type="button" class="marly-toggle-password" data-password-target="contrasena"
                                aria-label="Mostrar contraseña">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                            <small>La contraseña debe tener mínimo 8 caracteres.</small>
                        </div>
                    </div>

                    <div class="marly-edicion-fila">
                        <div class="marly-edicion-info">
                            <span class="marly-edicion-icono">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="5" y="11" width="14" height="10" rx="2"></rect>
                                    <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
                                </svg>
                            </span>
                            <div>
                                <label for="contrasena_confirmation">Confirmar nueva contraseña</label>
                                <small>Repite tu nueva contraseña.</small>
                            </div>
                        </div>

                        <div class="marly-edicion-password">
                            <input type="password" id="contrasena_confirmation" name="contrasena_confirmation"
                                minlength="8" placeholder="Confirma tu nueva contraseña">
                            <button type="button" class="marly-toggle-password"
                                data-password-target="contrasena_confirmation" aria-label="Mostrar contraseña">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="marly-edicion-footer">
                        <button type="submit" class="marly-edicion-guardar">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"></path>
                                <path d="M17 21v-8H7v8"></path>
                                <path d="M7 3v5h8"></path>
                            </svg>
                            Guardar cambios
                        </button>

                        <p>
                            <span>♡</span>
                            Tu información está protegida y solo tú puedes verla.
                        </p>
                    </div>
                </form>
            </div>

            <div class="tarjeta-cuenta tarjeta-eliminar marly-cuenta-card marly-cuenta-eliminar">
                <div class="marly-cuenta-eliminar-header">
                    <span class="marly-cuenta-eliminar-icono">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path>
                            <path d="M12 8v5"></path>
                            <path d="M12 16h.01"></path>
                        </svg>
                    </span>

                    <div>
                        <h2>Eliminar cuenta</h2>
                        <p>Esta acción eliminará tu cuenta de cliente del sistema. Si continúas, deberás registrarte
                            nuevamente para volver a ingresar.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('cliente.cuenta.eliminar') }}" class="rejilla-formulario">
                    @csrf
                    @method('DELETE')

                    <label class="campo-confirmacion marly-cuenta-confirmacion">
                        <input type="checkbox" name="confirmar_eliminacion" value="1">
                        Confirmo que deseo eliminar mi cuenta.
                    </label>

                    <div class="acciones-formulario">
                        <button type="submit" class="boton boton-peligro marly-cuenta-btn-eliminar">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 6h18"></path>
                                <path d="M8 6V4h8v2"></path>
                                <path d="M19 6l-1 14H6L5 6"></path>
                                <path d="M10 11v6"></path>
                                <path d="M14 11v6"></path>
                            </svg>
                            Eliminar cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
