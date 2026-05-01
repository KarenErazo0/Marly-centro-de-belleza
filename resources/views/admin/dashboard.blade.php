@extends('plantillas.app')

@section('title', 'Panel administradora | Marly Centro de Belleza')

@section('content')
<section class="panel-admin">
    <style>
        .panel-admin { padding: 2rem 0 4rem; }
        .admin-hero { background: linear-gradient(135deg, #1f2937, #374151); color: #fff; border-radius: 24px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 18px 45px rgba(31,41,55,.18); }
        .admin-hero p { margin: .5rem 0 0; color: #f3e8d3; }
        .admin-grid { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(320px, .95fr); gap: 1.5rem; align-items: start; }
        .admin-card { background: #fff; border: 1px solid #eadfce; border-radius: 22px; padding: 1.5rem; box-shadow: 0 12px 35px rgba(73, 54, 34, .08); }
        .admin-card h2, .admin-card h3 { margin-top: 0; color: #493622; }
        .calendario-top { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .calendario-top a { text-decoration: none; color: #8b5e34; font-weight: 700; }
        .calendario { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: .55rem; }
        .dia-nombre { text-align: center; font-weight: 700; font-size: .84rem; color: #7c6a55; padding: .4rem 0; }
        .dia-calendario { min-height: 78px; border-radius: 16px; border: 1px solid #eadfce; background: #fbf8f3; padding: .65rem; text-decoration: none; color: #493622; display: flex; flex-direction: column; justify-content: space-between; transition: .2s ease; }
        .dia-calendario:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(73,54,34,.1); }
        .dia-calendario.fuera-mes { opacity: .35; }
        .dia-calendario.con-citas { border-color: #b88746; background: #fff5e6; }
        .dia-calendario.seleccionado { outline: 3px solid rgba(184, 135, 70, .35); }
        .dia-calendario.hoy { border-color: #493622; }
        .numero-dia { font-weight: 800; }
        .contador-citas { align-self: flex-start; font-size: .76rem; font-weight: 700; background: #b88746; color: #fff; padding: .22rem .5rem; border-radius: 999px; }
        .lista-citas { display: grid; gap: .85rem; }
        .cita-admin { border: 1px solid #eadfce; background: #fbf8f3; border-radius: 16px; padding: 1rem; }
        .cita-admin strong { color: #493622; }
        .cita-meta { display: flex; flex-wrap: wrap; gap: .5rem; margin: .55rem 0; }
        .etiqueta { background: #efe5d5; color: #493622; border-radius: 999px; padding: .25rem .55rem; font-size: .82rem; font-weight: 700; }
        .estado-cita { text-transform: capitalize; }
        .sin-citas { border: 1px dashed #d8c8b5; color: #7c6a55; padding: 1rem; border-radius: 16px; background: #fffaf4; }
        .seccion-hoy { margin-top: 1.5rem; }
        @media (max-width: 900px) { .admin-grid { grid-template-columns: 1fr; } .dia-calendario { min-height: 62px; padding: .5rem; } }
    </style>

    <div class="contenedor">
        <div class="admin-hero">
            <h1>Panel de administradora</h1>
            <p>Visualiza las citas programadas por fecha y revisa rápidamente las citas del día de hoy.</p>
        </div>

        <div class="admin-grid">
            <article class="admin-card">
                <div class="calendario-top">
                    <a href="{{ route('admin.dashboard', ['mes' => $mesAnterior, 'fecha' => $fechaSeleccionada->format('Y-m-d')]) }}">← Mes anterior</a>
                    <h2>{{ ucfirst($mesActual->translatedFormat('F Y')) }}</h2>
                    <a href="{{ route('admin.dashboard', ['mes' => $mesSiguiente, 'fecha' => $fechaSeleccionada->format('Y-m-d')]) }}">Mes siguiente →</a>
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

    <a class="{{ $clases }}" href="{{ route('admin.dashboard', ['mes' => $mesActual->format('Y-m'), 'fecha' => $fecha]) }}">
        <span class="numero-dia">{{ $cursor->format('j') }}</span>

        @if($total > 0)
            <span class="contador-citas">{{ $total }} cita{{ $total === 1 ? '' : 's' }}</span>
        @endif
    </a>
@endforeach
                </div>
            </article>

            <aside class="admin-card">
                <h2>Citas del {{ $fechaSeleccionada->translatedFormat('d \d\e F \d\e Y') }}</h2>
                @include('admin.partials.lista-citas', ['citas' => $citasFechaSeleccionada])
            </aside>
        </div>

        <article class="admin-card seccion-hoy">
            <h2>Citas del día de hoy</h2>
            <p>{{ ucfirst($hoy->translatedFormat('l, d \d\e F \d\e Y')) }}</p>
            @include('admin.partials.lista-citas', ['citas' => $citasHoy])
        </article>
    </div>
</section>
@endsection
