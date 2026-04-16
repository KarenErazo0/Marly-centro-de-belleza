@extends('plantillas.app')

@section('title', 'Mis citas | Marly Centro de Belleza')

@section('content')
<section class="seccion-reservas">
    <div class="contenedor">
        <div class="encabezado-reserva-simple">
            <div>
                <span class="etiqueta">HU3 · Agendamiento</span>
                <h1>Mis citas</h1>
                <p>Consulta tus reservas registradas y agenda una nueva cita cuando lo necesites.</p>
            </div>
            <a href="{{ route('cliente.citas.agendar.servicios') }}" class="boton boton-primario">Agendar nueva cita</a>
        </div>

        @if($citas->isEmpty())
            <div class="tarjeta-reserva-vacia">
                <h3>Aún no tienes citas registradas</h3>
                <p>Selecciona uno o más servicios, elige tus profesionales y reserva el horario disponible.</p>
            </div>
        @else
            <div class="rejilla-citas-registradas">
                @foreach($citas as $cita)
                    <article class="tarjeta-cita-registrada">
                        <div class="tarjeta-cita-encabezado">
                            <div>
                                <span class="estado-cita">{{ ucfirst($cita->estado) }}</span>
                                <h3>{{ $cita->servicios->pluck('nombre_servicio')->join(', ') }}</h3>
                                @if($cita->detalles->isNotEmpty())
                                    @foreach($cita->detalles->groupBy('area') as $area => $detallesArea)
                                        @php $detalle = $detallesArea->first(); @endphp
                                        <p><strong>{{ $area ?: 'Servicio' }}:</strong> {{ optional($detalle->trabajador)->nombre_completo }}</p>
                                    @endforeach
                                @else
                                    <p>{{ optional($cita->trabajador)->nombre_completo }}</p>
                                @endif
                            </div>
                            <div class="fecha-cita-registrada">
                                <strong>{{ \Carbon\Carbon::parse($cita->fecha_cita)->translatedFormat('l, j \d\e F \d\e Y') }}</strong>
                                <span>{{ \Carbon\Carbon::createFromFormat('H:i:s', $cita->hora_inicio)->translatedFormat('h:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $cita->hora_fin)->translatedFormat('h:i A') }}</span>
                            </div>
                        </div>
                        <div class="chips-reserva">
                            @foreach($cita->servicios as $servicio)
                                <span class="chip-servicio">{{ $servicio->nombre_servicio }}</span>
                            @endforeach
                        </div>
                        <p><strong>Nombre:</strong> {{ $cita->nombre_cliente }}</p>
                        <p><strong>Teléfono:</strong> {{ $cita->telefono_contacto }}</p>
                        @if($cita->notas)
                            <p><strong>Notas:</strong> {{ $cita->notas }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
