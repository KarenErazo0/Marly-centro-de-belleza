@extends('plantillas.app')

@section('title', 'Seleccionar fecha y hora | Marly Centro de Belleza')

@section('content')
<section class="seccion-reservas">
    <div class="contenedor contenedor-reserva">
        <div class="pasos-reserva">
            <span class="paso-reserva completado">1. Servicios</span>
            <span class="paso-reserva completado">2. Profesional</span>
            <span class="paso-reserva activo">3. Fecha y hora</span>
            <span class="paso-reserva">4. Confirmación</span>
        </div>

        <div class="encabezado-seccion reserva-centro encabezado-horario-simple">
            <h1>Selecciona el horario de tu cita</h1>
        </div>

        <div class="aviso-horario-salon">
            <strong>Horario del salón:</strong> Lunes a viernes de 7:00 am a 7:00 pm y sábados o festivos de 8:00 am a 7:00 pm
        </div>

        <div class="tarjeta-horario tarjeta-horario-nueva">
            <form action="{{ route('cliente.citas.agendar.horario.guardar') }}" method="POST" id="form-seleccion-horario">
                @csrf

                <div class="layout-horario-resumen">
                    <div class="panel-selector-horario">
                        <div class="grupo-campo">
                            <label for="fecha_selector">Fecha de la cita</label>
                            <input
                                type="date"
                                id="fecha_selector"
                                name="fecha"
                                value="{{ $fechaSeleccionada }}"
                                min="{{ now()->format('Y-m-d') }}"
                                data-url-base="{{ route('cliente.citas.agendar.horario') }}"
                                required
                            >
                            <small class="ayuda-campo">Puedes escribir la fecha o seleccionarla desde el calendario. Los domingos no hay atención.</small>
                        </div>

                        <div class="grupo-campo">
                            <label for="hora_selector">Hora disponible</label>
                            <select id="hora_selector" name="hora" required>
                                <option value="">Selecciona una hora</option>
                                @forelse($horasDisponibles as $hora)
                                    <option value="{{ $hora }}" {{ ($reserva['hora'] ?? null) === $hora ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromFormat('H:i', $hora)->translatedFormat('h:i A') }}
                                    </option>
                                @empty
                                    <option value="" disabled>No hay horarios disponibles para esta fecha</option>
                                @endforelse
                            </select>
                        </div>
                    </div>

                    <aside class="resumen-horario-vivo">
                        <h3>Tu cita será:</h3>
                        <p><strong>Fecha:</strong> <span id="resumen-fecha-horario">{{ $fechaSeleccionada ? ucfirst(\Carbon\Carbon::parse($fechaSeleccionada)->translatedFormat('l, j \d\e F \d\e Y')) : 'Pendiente' }}</span></p>
                        <p><strong>Hora:</strong> <span id="resumen-hora-horario">{{ !empty($reserva['hora']) ? \Carbon\Carbon::createFromFormat('H:i', $reserva['hora'])->translatedFormat('h:i A') : 'Pendiente' }}</span></p>
                        <div class="linea-resumen"></div>
                        <p><strong>Servicio:</strong> {{ $servicios->pluck('nombre_servicio')->join(', ') }}</p>
                        <p><strong>Profesionales seleccionados:</strong> {{ $trabajadores->pluck('nombre_completo')->join(', ') }}</p>
                    </aside>
                </div>

                <div class="acciones-reserva-final">
                    <a href="{{ route('cliente.citas.agendar.trabajador') }}" class="boton boton-secundario">Volver</a>
                    <button type="submit" class="boton boton-primario">Continuar</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
