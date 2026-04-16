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

        <div class="encabezado-seccion reserva-centro">
            <h1>Selecciona fecha y hora</h1>
            <p>Elige el día y la hora en que todos los profesionales seleccionados estén disponibles.</p>
        </div>

        <div class="tarjeta-horario">
            <div class="resumen-agendamiento-top">
                <div>
                    <strong>Profesionales seleccionados:</strong>
                    <div class="chips-reserva chips-profesionales">
                        @foreach($trabajadores as $trabajador)
                            <span class="chip-servicio">{{ $trabajador->nombre_completo }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="chips-reserva">
                    @foreach($servicios as $servicio)
                        <span class="chip-servicio">{{ $servicio->nombre_servicio }}</span>
                    @endforeach
                </div>
                <div><strong>Duración estimada:</strong> {{ $duracionTotal }} minutos</div>
            </div>

            <form action="{{ route('cliente.citas.agendar.horario.guardar') }}" method="POST" id="form-seleccion-horario">
                @csrf
                <div class="grupo-seleccion-horario">
                    <h3>Selecciona una fecha</h3>
                    <div class="rejilla-fechas">
                        @foreach($diasDisponibles as $dia)
                            <a href="{{ route('cliente.citas.agendar.horario', ['fecha' => $dia['fecha']]) }}" class="item-fecha-link">
                                <span class="item-fecha {{ $fechaSeleccionada === $dia['fecha'] ? 'seleccionado' : '' }}">
                                    <span class="dia-semana">{{ $dia['dia_semana'] }}</span>
                                    <strong>{{ $dia['dia'] }}</strong>
                                    <span>{{ $dia['mes'] }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <input type="hidden" name="fecha" value="{{ $fechaSeleccionada }}">

                <div class="grupo-seleccion-horario">
                    <h3>Selecciona una hora</h3>
                    <div class="rejilla-horas">
                        @forelse($horasDisponibles as $hora)
                            <label class="item-hora item-hora-opcion {{ ($reserva['hora'] ?? null) === $hora ? 'seleccionado' : '' }}">
                                <input type="radio" name="hora" value="{{ $hora }}" {{ ($reserva['hora'] ?? null) === $hora ? 'checked' : '' }}>
                                {{ \Carbon\Carbon::createFromFormat('H:i', $hora)->translatedFormat('h:i A') }}
                            </label>
                        @empty
                            <p class="mensaje-sin-horas">No hay horarios disponibles para la fecha seleccionada.</p>
                        @endforelse
                    </div>
                </div>

                <div class="resumen-cita-previa">
                    <h4>Tu cita será:</h4>
                    <p><strong>Fecha:</strong> {{ $fechaSeleccionada ? \Carbon\Carbon::parse($fechaSeleccionada)->translatedFormat('l, j \d\e F \d\e Y') : 'Pendiente' }}</p>
                    <p><strong>Hora:</strong> {{ !empty($reserva['hora']) ? \Carbon\Carbon::createFromFormat('H:i', $reserva['hora'])->translatedFormat('h:i A') : 'Pendiente' }}</p>
                    <p><strong>Horario del salón:</strong> lunes a viernes de 7:00 a. m. a 7:00 p. m. y sábados de 8:00 a. m. a 7:00 p. m.</p>
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
