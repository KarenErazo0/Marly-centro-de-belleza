@extends('plantillas.app')

@section('title', 'Modificar cita | Marly Centro de Belleza')

@section('content')
    <section class="seccion-reservas">
        <div class="contenedor contenedor-confirmacion">
            <div class="encabezado-reserva-simple">
                <div>
                    <h1>Modificar cita</h1>
                    <p class="texto-suave">Ajusta la fecha, hora, teléfono o notas de tu cita registrada.</p>
                </div>
                <a href="{{ route('cliente.citas.index') }}" class="boton boton-secundario">Volver a mis citas</a>
            </div>

            <article class="tarjeta-confirmacion-reserva tarjeta-editar-cita">
                <div class="resumen-confirmacion-pequeno">
                    <h3>Resumen actual</h3>
                    <p><strong>Servicio:</strong> {{ $cita->servicios->pluck('nombre_servicio')->join(', ') ?: 'Servicio' }}
                    </p>
                    <p><strong>Profesional:</strong>
                        @php($profesionales = $cita->detalles->pluck('trabajador.nombre_completo')->filter()->unique()->values())
                        {{ $profesionales->isNotEmpty() ? $profesionales->join(', ') : optional($cita->trabajador)->nombre_completo }}
                    </p>
                    <p><strong>Fecha actual:</strong>
                        {{ ucfirst(\Carbon\Carbon::parse($cita->fecha_cita)->translatedFormat('l, j \d\e F \d\e Y')) }}</p>
                    <p><strong>Hora actual:</strong>
                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $cita->hora_inicio)->format('h:i A') }} -
                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $cita->hora_fin)->format('h:i A') }}</p>
                </div>

                <form id="form-modificar-cita" class="formulario datos-confirmacion-cliente"
                    action="{{ route('cliente.citas.actualizar', $cita) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grupo-campo">
                        <label for="fecha">Nueva fecha</label>
                        <input type="date" id="fecha" name="fecha"
                            min="{{ now('America/Bogota')->format('Y-m-d') }}"
                            value="{{ old('fecha', $fechaSeleccionada) }}"
                            data-url-base="{{ route('cliente.citas.editar', $cita) }}" required>
                        <small>Al cambiar la fecha se actualizarán las horas disponibles.</small>
                    </div>

                    <div class="grupo-campo">
                        <label for="hora">Nueva hora disponible</label>
                        <select id="hora" name="hora" required>
                            <option value="">Selecciona una hora</option>
                            @foreach ($horasDisponibles as $hora)
                                <option value="{{ $hora }}" @selected(old('hora', \Carbon\Carbon::parse($cita->hora_inicio)->format('H:i')) === $hora)>{{ $hora }}
                                </option>
                            @endforeach
                        </select>
                        @if ($horasDisponibles->isEmpty())
                            <small>No hay horarios disponibles para esta fecha.</small>
                        @endif
                    </div>

                    <div class="grupo-campo">
                        <label for="telefono_contacto">Teléfono de contacto</label>
                        <input type="text" id="telefono_contacto" name="telefono_contacto"
                            value="{{ old('telefono_contacto', $cita->telefono_contacto) }}" required>
                    </div>

                    <div class="grupo-campo">
                        <label for="notas">Notas adicionales</label>
                        <textarea id="notas" name="notas" rows="4"
                            placeholder="Ejemplo: preferencia de horario, detalles del servicio o comentarios adicionales">{{ old('notas', $cita->notas) }}</textarea>
                    </div>

                    <div class="acciones-reserva-final">
                        <a href="{{ route('cliente.citas.index') }}" class="boton boton-secundario">Cancelar cambios</a>
                        <button type="submit" class="boton boton-primario" data-confirm-title="Confirmar modificación"
                            data-confirm-message="¿Estas seguro de modificar esta cita?"
                            data-confirm-detail="Se actualizará la información de la cita en el sistema de agendamiento."
                            data-confirm-action="Guardar modificación">Guardar cambios</button>
                    </div>
                </form>
            </article>
        </div>
    </section>
@endsection
