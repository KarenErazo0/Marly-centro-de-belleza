@if($citas->isEmpty())
    <div class="sin-citas">No hay citas programadas para esta fecha.</div>
@else
    <div class="lista-citas">
        @foreach($citas as $cita)
            <div class="cita-admin">
                <strong>{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i a') }} - {{ \Carbon\Carbon::parse($cita->hora_fin)->format('h:i a') }}</strong>
                <div class="cita-meta">
                    <span class="etiqueta estado-cita">{{ $cita->estado }}</span>
                    <span class="etiqueta">{{ $cita->duracion_total_minutos }} min</span>
                </div>
                <p><strong>Cliente:</strong> {{ $cita->nombre_cliente ?? optional($cita->cliente)->nombre_completo }}</p>
                <p><strong>Teléfono:</strong> {{ $cita->telefono_contacto ?? optional($cita->cliente)->telefono }}</p>
                <p><strong>Servicios:</strong> {{ $cita->servicios->pluck('nombre_servicio')->join(', ') ?: 'Sin servicios registrados' }}</p>
                <p><strong>Profesionales:</strong>
                    @php($profesionales = $cita->detalles->pluck('trabajador.nombre_completo')->filter()->unique()->values())
                    {{ $profesionales->isNotEmpty() ? $profesionales->join(', ') : optional($cita->trabajador)->nombre_completo }}
                </p>
                @if($cita->notas)
                    <p><strong>Notas:</strong> {{ $cita->notas }}</p>
                @endif
            </div>
        @endforeach
    </div>
@endif
