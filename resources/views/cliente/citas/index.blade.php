@extends('plantillas.app')

@section('title', 'Mis citas | Marly Centro de Belleza')

@section('content')
<section class="seccion-reservas seccion-mis-citas">
    <div class="contenedor contenedor-mis-citas">

        <div class="encabezado-mis-citas">
            <div>
                <h1>Mis citas</h1>
                <p>Consulta, busca, modifica o cancela tus citas activas.</p>
            </div>

            <a href="{{ route('cliente.citas.agendar.servicios') }}" class="boton boton-primario boton-agendar-cita">
                <span>+</span>
                Agendar nueva cita
            </a>
        </div>

        <form class="barra-busqueda-citas barra-busqueda-mis-citas" action="{{ route('cliente.citas.index') }}" method="GET">
            <div class="campo-busqueda-citas">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>

                <input type="search" name="buscar" value="{{ $busqueda }}" placeholder="Buscar por servicio, nombre o teléfono. Ej: manicure">
            </div>

            <button type="submit" class="boton boton-primario">Buscar</button>

            @if($busqueda)
                <a href="{{ route('cliente.citas.index') }}" class="boton boton-secundario">Limpiar</a>
            @endif
        </form>

        @if($citas->isEmpty())
            <div class="tarjeta-reserva-vacia tarjeta-vacia-mis-citas">
                <h3>{{ $busqueda ? 'No se encontraron citas' : 'Aún no tienes citas registradas' }}</h3>
                <p>{{ $busqueda ? 'Intenta buscar con otro servicio, nombre o teléfono.' : 'Selecciona uno o más servicios, elige tus profesionales y reserva el horario disponible.' }}</p>
            </div>
        @else
            <div class="grupos-citas-tiempo grupos-mis-citas">
                @foreach($citasAgrupadas as $tituloGrupo => $citasGrupo)
                    <section class="grupo-tiempo-citas grupo-mis-citas">
                        <div class="grupo-tiempo-header header-mis-citas">
                            <div>
                                <h2>{{ $tituloGrupo }}</h2>
                                <span class="linea-titulo-citas"></span>
                            </div>

                            <span>{{ $citasGrupo->count() }} cita{{ $citasGrupo->count() === 1 ? '' : 's' }}</span>
                        </div>

                        <div class="rejilla-citas-registradas rejilla-mis-citas">
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

                                    $profesionales = $cita->detalles->pluck('trabajador.nombre_completo')->filter()->unique()->values();
                                    $servicios = $cita->servicios->pluck('nombre_servicio')->join(', ') ?: 'Servicio';
                                    $profesionalTexto = $profesionales->isNotEmpty() ? $profesionales->join(', ') : optional($cita->trabajador)->nombre_completo;
                                @endphp

                                <article class="tarjeta-cita-registrada tarjeta-cita-limpia tarjeta-cita-con-menu tarjeta-mis-citas">
                                    @if($puedeGestionarse)
                                        <div class="menu-cita-cliente">
                                            <button type="button" class="boton-menu-cita" aria-label="Opciones de cita">•••</button>

                                            <div class="opciones-menu-cita">
                                                <a href="{{ route('cliente.citas.editar', $cita) }}">Modificar cita</a>

                                                <form action="{{ route('cliente.citas.cancelar', $cita) }}" method="POST" class="form-confirmable">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        data-confirm-title="Cancelar cita"
                                                        data-confirm-message="¿Estas seguro de cancelar la cita?"
                                                        data-confirm-detail="Recuerda que se marcará como cancelada y se liberará del sistema de agendamiento."
                                                        data-confirm-action="Sí, cancelar cita">
                                                        Cancelar cita
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <div class="menu-cita-cliente menu-cita-solo-icono">
                                            <button type="button" class="boton-menu-cita" aria-label="Opciones de cita">•••</button>
                                        </div>
                                    @endif

                                    <div class="tarjeta-cita-encabezado">
                                        <span class="estado-cita {{ $estadoClase }}">{{ $estadoTexto }}</span>
                                        <h3>{{ $servicios }}</h3>
                                    </div>

                                    <div class="detalle-cita-grid detalle-mis-citas">
                                        <p>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 21a8 8 0 0 0-16 0"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                            <strong>Profesional:</strong>
                                            <span>{{ $profesionalTexto }}</span>
                                        </p>

                                        <p>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                                <path d="M16 2v4"></path>
                                                <path d="M8 2v4"></path>
                                                <path d="M3 10h18"></path>
                                            </svg>
                                            <strong>Fecha:</strong>
                                            <span>{{ ucfirst(\Carbon\Carbon::parse($cita->fecha_cita)->translatedFormat('l, j \d\e F \d\e Y')) }}</span>
                                        </p>

                                        <p>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <path d="M12 6v6l4 2"></path>
                                            </svg>
                                            <strong>Hora:</strong>
                                            <span>{{ \Carbon\Carbon::createFromFormat('H:i:s', $cita->hora_inicio)->translatedFormat('h:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $cita->hora_fin)->translatedFormat('h:i A') }}</span>
                                        </p>
                                    </div>

                                    <div class="footer-tarjeta-cita">
                                        <p><strong>Nombre:</strong> {{ $cita->nombre_cliente }}</p>
                                        <span></span>
                                        <p><strong>Teléfono:</strong> {{ $cita->telefono_contacto }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <div class="aviso-mis-citas">
                <div class="icono-aviso-citas">i</div>
                <div>
                    <strong>¿Necesitas modificar o cancelar una cita?</strong>
                    <p>Puedes hacerlo desde el menú de opciones de cada cita pendiente.</p>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection