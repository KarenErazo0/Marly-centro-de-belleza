@extends('plantillas.app')

@section('title', 'Confirmar cita | Marly Centro de Belleza')

@section('content')
    <section class="seccion-reservas">



        {{-- PASOS ACTUALES DEL PROCESO --}}
        <div class="pasos-reserva">
            <div class="paso-reserva completado">
                <span>1</span>
                Servicios
            </div>

            <div class="paso-reserva completado">
                <span>2</span>
                Profesional
            </div>

            <div class="paso-reserva completado">
                <span>3</span>
                Fecha y hora
            </div>

            <div class="paso-reserva activo">
                <span>4</span>
                Confirmación
            </div>
        </div>
        <div class="contenedor contenedor-reserva contenedor-confirmacion marly-confirmacion-marco">
            <div class="marly-confirmacion-header">
                <h1>Resumen de tu reserva</h1>
                <p>Revisa el resumen y confirma tus datos antes de finalizar la reserva.</p>
            </div>

            <form action="{{ route('cliente.citas.agendar.registrar') }}" method="POST" class="marly-confirmacion-form"
                id="form-confirmar-reserva">
                @csrf

                <aside class="marly-resumen-card">
                    <h2>Resumen de la cita</h2>

                    <div class="marly-resumen-linea">
                        <strong>Servicio</strong>
                        <span>{{ $servicios->pluck('nombre_servicio')->join(', ') }}</span>
                    </div>

                    <div class="marly-resumen-linea">
                        <strong>Profesional</strong>
                        <span>{{ $trabajadores->pluck('nombre_completo')->join(', ') }}</span>
                    </div>

                    <div class="marly-resumen-linea">
                        <strong>Fecha</strong>
                        <span>{{ ucfirst(\Carbon\Carbon::parse($reserva['fecha'])->translatedFormat('l, j \d\e F \d\e Y')) }}</span>
                    </div>

                    <div class="marly-resumen-linea">
                        <strong>Hora</strong>
                        <span>{{ \Carbon\Carbon::createFromFormat('H:i', $reserva['hora'])->translatedFormat('h:i A') }}</span>
                    </div>

                    <div class="marly-resumen-linea">
                        <strong>Duración estimada</strong>
                        <span>{{ $duracionTotal }} minutos</span>
                    </div>

                    <div class="marly-resumen-linea">
                        <strong>Precio estimado</strong>
                        <span>${{ number_format($servicios->sum('precio'), 0, ',', '.') }}</span>
                    </div>

                    <div class="marly-aviso-precio">
                        <span>i</span>
                        <p>El precio es estimado y puede variar según el diseño, materiales o detalles finales del servicio.
                        </p>
                    </div>
                </aside>

                <div class="marly-datos-card">
                    <div class="grupo-campo marly-campo-confirmacion">
                        <label for="nombre_cliente">Nombre completo *</label>
                        <input type="text" id="nombre_cliente" name="nombre_cliente"
                            value="{{ old('nombre_cliente', $cliente->nombre_completo) }}" required>
                    </div>

                    <div class="grupo-campo marly-campo-confirmacion">
                        <label for="telefono_contacto">Teléfono *</label>
                        <input type="text" id="telefono_contacto" name="telefono_contacto"
                            value="{{ old('telefono_contacto', $cliente->telefono) }}" required>
                    </div>

                    <div class="grupo-campo marly-campo-confirmacion">
                        <label for="notas">Notas adicionales (opcional)</label>
                        <textarea id="notas" name="notas" rows="4" placeholder="¿Alguna preferencia o solicitud especial?">{{ old('notas') }}</textarea>
                        <small>Cuéntanos cualquier detalle que debamos tener en cuenta para tu cita.</small>
                    </div>
                </div>

                <div class="marly-confirmacion-acciones">
                    <a href="{{ route('cliente.citas.agendar.horario') }}" class="marly-btn-volver">
                        <span>←</span>
                        Volver
                    </a>

                    <button type="submit" class="marly-btn-confirmar">
                        Confirmar cita
                    </button>
                </div>
            </form>
        </div>

        <div class="modal-confirmacion-cita" id="modal-confirmacion-cita" aria-hidden="true">
            <div class="modal-confirmacion-backdrop" data-modal-cancelar></div>

            <div class="modal-confirmacion-card" role="dialog" aria-modal="true"
                aria-labelledby="titulo-modal-confirmacion">
                <span class="etiqueta">Confirmación</span>

                <p id="titulo-modal-confirmacion">¿Estás seguro de agendar esta cita?</p>

                <small>
                    Recuerda llegar a tiempo. El precio es estimado y puede variar dependiendo del diseño o detalles finales
                    del servicio.
                </small>

                <div class="acciones-modal-confirmacion">
                    <button type="button" class="boton boton-secundario" data-modal-cancelar>
                        Revisar de nuevo
                    </button>

                    <button type="button" class="boton boton-primario" id="btn-modal-confirmar-cita">
                        Sí, confirmar cita
                    </button>
                </div>
            </div>
        </div>

    </section>
@endsection
