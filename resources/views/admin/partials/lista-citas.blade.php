@if ($citas->isEmpty())
    <div class="sin-citas">No hay citas registradas para mostrar.</div>
@else
    <div class="lista-citas">
        @foreach ($citas as $cita)
            @php
                $inicioCita = \Carbon\Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $cita->fecha_cita . ' ' . $cita->hora_inicio,
                    'America/Bogota',
                );
                $estadoTexto = match ($cita->estado) {
                    'completada' => 'Sí asistió',
                    'inasistencia' => 'No asistió',
                    'cancelada' => 'Cancelada',
                    default => $inicioCita->isPast() ? 'Pendiente · ya pasó' : 'Pendiente',
                };
                $estadoClase = match ($cita->estado) {
                    'completada' => 'estado-completada',
                    'inasistencia' => 'estado-inasistencia',
                    'cancelada' => 'estado-cancelada',
                    default => 'estado-pendiente',
                };
                $puedeMarcarAsistencia = in_array($cita->estado, ['registrada', 'confirmada'], true);
            @endphp
            <div class="cita-admin">
                <strong>{{ \Carbon\Carbon::parse($cita->fecha_cita)->translatedFormat('d/m/Y') }} ·
                    {{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i a') }} -
                    {{ \Carbon\Carbon::parse($cita->hora_fin)->format('h:i a') }}</strong>
                <div class="cita-meta">
                    <span class="etiqueta-admin estado-cita {{ $estadoClase }}">{{ $estadoTexto }}</span>
                    <span class="etiqueta-admin">{{ $cita->duracion_total_minutos }} min</span>
                </div>
                <p><strong>Cliente:</strong> {{ $cita->nombre_cliente ?? optional($cita->cliente)->nombre_completo }}
                </p>
                <p><strong>Teléfono:</strong> {{ $cita->telefono_contacto ?? optional($cita->cliente)->telefono }}</p>
                <p><strong>Servicio:</strong>
                    {{ $cita->servicios->pluck('nombre_servicio')->join(', ') ?: 'Sin servicios registrados' }}</p>
                <p><strong>Profesional:</strong>
                    @php($profesionales = $cita->detalles->pluck('trabajador.nombre_completo')->filter()->unique()->values())
                    {{ $profesionales->isNotEmpty() ? $profesionales->join(', ') : optional($cita->trabajador)->nombre_completo }}
                </p>
                @if ($cita->notas)
                    <p><strong>Notas:</strong> {{ $cita->notas }}</p>
                @endif

                @if ($puedeMarcarAsistencia)
                    <div class="acciones-asistencia-admin">
                        <form action="{{ route('admin.citas.asistencia', $cita) }}" method="POST"
                            class="form-confirmable">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="estado" value="completada">
                            <button type="submit" class="btn-asistencia btn-asistio"
                                data-confirm-title="Confirmar asistencia"
                                data-confirm-message="¿Estas seguro que el cliente sí asistió?"
                                data-confirm-detail="Esta información se reflejará también en el panel del cliente como cita cumplida."
                                data-confirm-action="Sí, asistió"><span>✓</span> Sí asistió</button>
                        </form>
                        <form action="{{ route('admin.citas.asistencia', $cita) }}" method="POST"
                            class="form-confirmable">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="estado" value="inasistencia">
                            <button type="submit" class="btn-asistencia btn-no-asistio"
                                data-confirm-title="Confirmar inasistencia"
                                data-confirm-message="¿Estas seguro que el cliente no asistió?"
                                data-confirm-detail="Esta información se reflejará también en el panel del cliente como cita no cumplida."
                                data-confirm-action="Sí, no asistió"><span>×</span> No asistió</button>
                        </form>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif
