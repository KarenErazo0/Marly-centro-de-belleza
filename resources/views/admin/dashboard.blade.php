@extends('plantillas.app')

@section('title', 'Panel administradora | Marly Centro de Belleza')

@section('content')
<section class="panel-admin">
    <style>
        .panel-admin { padding: 2rem 0 4rem; }
        .admin-hero { background: linear-gradient(135deg, #1A1A1A, #2F2F2F); color: #fff; border-radius: 28px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 18px 45px rgba(45,32,36,.18); border: 1px solid var(--color-borde); }
        .admin-hero p { margin: .5rem 0 0; color: #E0C97A; }
        .admin-seccion { margin-top: 1.5rem; }
        .admin-card { background: var(--color-superficie); border: 1px solid var(--color-borde); border-radius: 22px; padding: 1.5rem; box-shadow: var(--sombra-suave); }
        .admin-card h2, .admin-card h3 { margin-top: 0; color: var(--color-texto); }
        .admin-card-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem; }
        .admin-card-header p { margin: .25rem 0 0; color: var(--color-texto-suave); }
        .calendario-panel { display: none; margin-top: 1rem; }
        .calendario-panel.visible { display: block; }
        .calendario-top { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .calendario-top a { text-decoration: none; color: var(--color-primario); font-weight: 700; }
        .calendario { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: .55rem; }
        .dia-nombre { text-align: center; font-weight: 700; font-size: .84rem; color: var(--color-primario); padding: .4rem 0; }
        .dia-calendario { min-height: 78px; border-radius: 16px; border: 1px solid var(--color-borde); background: var(--color-superficie-suave); padding: .65rem; text-decoration: none; color: var(--color-texto); display: flex; flex-direction: column; justify-content: space-between; transition: .2s ease; }
        .dia-calendario:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(201,166,70,.12); }
        .dia-calendario.fuera-mes { opacity: .35; }
        .dia-calendario.con-citas { border-color: var(--color-primario); background: color-mix(in srgb, var(--color-primario) 12%, var(--color-superficie)); }
        .dia-calendario.seleccionado { outline: 3px solid color-mix(in srgb, var(--color-primario) 28%, transparent); }
        .dia-calendario.hoy { border-color: var(--color-texto); }
        .numero-dia { font-weight: 800; }
        .contador-citas { display: inline-flex; align-self: flex-start; padding: 5px 8px; border-radius: 999px; background: var(--color-primario); color: #1A1A1A; font-size: .76rem; font-weight: 800; }
        .lista-citas { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; align-items: stretch; }
        .cita-admin { background: var(--color-superficie-suave); border: 1px solid var(--color-borde); border-radius: 18px; padding: 1rem; min-height: 100%; display: flex; flex-direction: column; gap: .6rem; }
        .cita-admin p { margin: 0; color: var(--color-texto-suave); line-height: 1.45; }
        .cita-admin strong { color: var(--color-texto); }
        .cita-meta { display: flex; gap: .45rem; flex-wrap: wrap; }
        .etiqueta-admin { display: inline-flex; width: fit-content; padding: 6px 10px; border-radius: 999px; background: color-mix(in srgb, var(--color-primario) 14%, var(--color-superficie)); color: var(--color-primario); font-weight: 800; font-size: .82rem; }
        .sin-citas { padding: 1rem; border-radius: 16px; border: 1px dashed var(--color-borde); color: var(--color-texto-suave); background: var(--color-superficie-suave); }
        @media (max-width: 1050px) { .lista-citas { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 650px) { .lista-citas, .calendario { grid-template-columns: 1fr; } .dia-nombre { display: none; } }
    </style>

    <div class="contenedor">
        <div class="admin-hero">
            <h1>Panel administrativo</h1>
            <p>Gestiona las citas, registra asistencia y consulta reservas por nombre, teléfono o servicio.</p>
        </div>

        <form class="barra-busqueda-citas" action="{{ route('admin.dashboard') }}" method="GET">
            <input type="search" name="buscar" value="{{ $busqueda }}" placeholder="Buscar por nombre, teléfono o servicio">
            <button type="submit" class="boton boton-primario">Buscar</button>
            @if($busqueda)
                <a href="{{ route('admin.dashboard') }}" class="boton boton-secundario">Limpiar</a>
            @endif
        </form>

        <article class="admin-card admin-seccion">
            <div class="admin-card-header">
                <div>
                    <h2>Citas del día de hoy</h2>
                    <p>{{ ucfirst($hoy->translatedFormat('l, d \d\e F \d\e Y')) }}</p>
                </div>
            </div>
            @include('admin.partials.lista-citas', ['citas' => $citasHoy])
        </article>

        <article class="admin-card admin-seccion">
            <div class="admin-card-header">
                <div>
                    <h2>Todas las citas</h2>
                    <p>Organizadas por secciones según su fecha.</p>
                </div>
                <button type="button" class="boton boton-secundario" id="btn-ver-calendario-admin">Ver citas en calendario</button>
            </div>

            <div class="calendario-panel" id="calendario-admin-panel">
                <div class="calendario-top">
                    <a href="{{ route('admin.dashboard', ['mes' => $mesAnterior, 'fecha' => $fechaSeleccionada->format('Y-m-d'), 'buscar' => $busqueda]) }}">← Mes anterior</a>
                    <h3>{{ ucfirst($mesActual->translatedFormat('F Y')) }}</h3>
                    <a href="{{ route('admin.dashboard', ['mes' => $mesSiguiente, 'fecha' => $fechaSeleccionada->format('Y-m-d'), 'buscar' => $busqueda]) }}">Mes siguiente →</a>
                </div>

                <div class="calendario">
                    @foreach(['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $dia)
                        <div class="dia-nombre">{{ $dia }}</div>
                    @endforeach

                    @php
                        $diasCalendario = [];
                        $cursor = $inicioCalendario->copy();

                        while ($cursor->lte($finCalendario)) {
                            $diasCalendario[] = $cursor->copy();
                            $cursor->addDay();
                        }
                    @endphp

                    @foreach($diasCalendario as $cursor)
                        @php
                            $fecha = $cursor->format('Y-m-d');
                            $total = (int) ($citasPorDia[$fecha] ?? 0);
                            $clases = 'dia-calendario';

                            if (! $cursor->isSameMonth($mesActual)) $clases .= ' fuera-mes';
                            if ($total > 0) $clases .= ' con-citas';
                            if ($cursor->isSameDay($fechaSeleccionada)) $clases .= ' seleccionado';
                            if ($cursor->isToday()) $clases .= ' hoy';
                        @endphp

                        <a class="{{ $clases }}" href="{{ route('admin.dashboard', ['mes' => $mesActual->format('Y-m'), 'fecha' => $fecha, 'buscar' => $busqueda]) }}#calendario-admin-panel">
                            <span class="numero-dia">{{ $cursor->format('j') }}</span>

                            @if($total > 0)
                                <span class="contador-citas">{{ $total }} cita{{ $total === 1 ? '' : 's' }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div class="admin-card" style="margin-top:1rem;">
                    <h3>Citas del {{ $fechaSeleccionada->translatedFormat('d \d\e F \d\e Y') }}</h3>
                    @include('admin.partials.lista-citas', ['citas' => $citasFechaSeleccionada])
                </div>
            </div>

            <div id="listado-admin-panel" class="listado-admin-panel grupos-citas-tiempo">
                @forelse($citasAgrupadas as $tituloGrupo => $citasGrupo)
                    <section class="grupo-tiempo-citas">
                        <div class="grupo-tiempo-header">
                            <h2>{{ $tituloGrupo }}</h2>
                            <span>{{ $citasGrupo->count() }} cita{{ $citasGrupo->count() === 1 ? '' : 's' }}</span>
                        </div>
                        @include('admin.partials.lista-citas', ['citas' => $citasGrupo])
                    </section>
                @empty
                    <div class="sin-citas">No se encontraron citas con los criterios seleccionados.</div>
                @endforelse
            </div>
        </article>
    </div>
</section>
@endsection
