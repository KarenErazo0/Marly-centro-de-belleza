@extends('plantillas.app')

@section('title', 'Mis citas | Marly Centro de Belleza')

@section('content')
<section class="seccion-reservas">
    <div class="contenedor">
        <div class="encabezado-reserva-simple">
            <div>
                <h1>Citas registradas</h1>
                <p class="texto-suave">Consulta, busca, modifica o cancela tus citas activas.</p>
            </div>
            <a href="{{ route('cliente.citas.agendar.servicios') }}" class="boton boton-primario">Agendar nueva cita</a>
        </div>

        <form class="barra-busqueda-citas" action="{{ route('cliente.citas.index') }}" method="GET">
            <input type="search" name="buscar" value="{{ $busqueda }}" placeholder="Buscar por servicio, nombre o teléfono. Ej: manicure">
            <button type="submit" class="boton boton-primario">Buscar</button>
            @if($busqueda)
                <a href="{{ route('cliente.citas.index') }}" class="boton boton-secundario">Limpiar</a>
            @endif
        </form>

        @if($citas->isEmpty())
            <div class="tarjeta-reserva-vacia">
                <h3>{{ $busqueda ? 'No se encontraron citas' : 'Aún no tienes citas registradas' }}</h3>
                <p>{{ $busqueda ? 'Intenta buscar con otro servicio, nombre o teléfono.' : 'Selecciona uno o más servicios, elige tus profesionales y reserva el horario disponible.' }}</p>
            </div>
        @else
            <div class="grupos-citas-tiempo">
                @foreach($citasAgrupadas as $tituloGrupo => $citasGrupo)
                    <section class="grupo-tiempo-citas">
                        <div class="grupo-tiempo-header">
                            <h2>{{ $tituloGrupo }}</h2>
                            <span>{{ $citasGrupo->count() }} cita{{ $citasGrupo->count() === 1 ? '' : 's' }}</span>
                        </div>

                        <div class="rejilla-citas-registradas">
                            @foreach($citasGrupo as $cita)
                                @php
                                    $inicioCita = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $cita->fecha_cita . ' ' . $cita->hora_inicio);
                                    $puedeGestionarse = $inicioCita->isFuture() && in_array($cita->estado, ['registrada', 'confirmada'], true);
                                    $estadoTexto = match($cita->estado) {
                                        'completada' => 'Cita cumplida',
                                        'inasistencia' => 'No asistió',
                                        'cancelada' => 'Cancelada',
                                        default => $inicioCita->isPast() ? 'Cita pasada' : 'Pendiente',
                                    };
                                    $estadoClase = match($cita->estado) {
                                        'completada' => 'estado-completada',
                                        'inasistencia' => 'estado-inasistencia',
                                        'cancelada' => 'estado-cancelada',
                                        default => $inicioCita->isPast() ? 'estado-pasada' : 'estado-pendiente',
                                    };
                                @endphp

                                <article class="tarjeta-cita-registrada tarjeta-cita-limpia tarjeta-cita-con-menu">
                                    @if($puedeGestionarse)
                                        <div class="menu-cita-cliente">
                                            <button type="button" class="boton-menu-cita" aria-label="Opciones de cita">•••</button>
                                            <div class="opciones-menu-cita">
                                                <a href="{{ route('cliente.citas.editar', $cita) }}">Modificar cita</a>
                                                <form action="{{ route('cliente.citas.cancelar', $cita) }}" method="POST" class="form-confirmable">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" data-confirm-title="Cancelar cita" data-confirm-message="¿Estas seguro de cancelar la cita?" data-confirm-detail="Recuerda que se marcará como cancelada y se liberará del sistema de agendamiento." data-confirm-action="Sí, cancelar cita">Cancelar cita</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="tarjeta-cita-encabezado">
                                        <span class="estado-cita {{ $estadoClase }}">{{ $estadoTexto }}</span>
                                        <h3>Cita para: {{ $cita->servicios->pluck('nombre_servicio')->join(', ') ?: 'Servicio' }}</h3>
                                    </div>

                                    <div class="detalle-cita-grid">
                                        <p><strong>Profesional:</strong>
                                            @php($profesionales = $cita->detalles->pluck('trabajador.nombre_completo')->filter()->unique()->values())
                                            {{ $profesionales->isNotEmpty() ? $profesionales->join(', ') : optional($cita->trabajador)->nombre_completo }}
                                        </p>
                                        <p><strong>Fecha:</strong> {{ ucfirst(\Carbon\Carbon::parse($cita->fecha_cita)->translatedFormat('l, j \d\e F \d\e Y')) }}</p>
                                        <p><strong>Hora:</strong> {{ \Carbon\Carbon::createFromFormat('H:i:s', $cita->hora_inicio)->translatedFormat('h:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $cita->hora_fin)->translatedFormat('h:i A') }}</p>
                                        <p><strong>Nombre:</strong> {{ $cita->nombre_cliente }}</p>
                                        <p><strong>Teléfono:</strong> {{ $cita->telefono_contacto }}</p>
                                        @if($cita->notas)
                                            <p><strong>Notas:</strong> {{ $cita->notas }}</p>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
