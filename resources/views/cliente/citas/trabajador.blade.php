@extends('plantillas.app')

@section('title', 'Seleccionar profesional | Marly Centro de Belleza')

@section('content')
<section class="seccion-reservas">
    <div class="contenedor contenedor-reserva">
        <div class="pasos-reserva">
            <span class="paso-reserva completado">1. Servicios</span>
            <span class="paso-reserva activo">2. Profesional</span>
            <span class="paso-reserva">3. Fecha y hora</span>
            <span class="paso-reserva">4. Confirmación</span>
        </div>

        <div class="encabezado-seccion reserva-centro">
            <h1>Seleccciona tu estilista de preferencia</h1>
            <p>Contamos con profesionales capacitados y apasionados por resaltar tu belleza. Puedes seleccionar un estilista o dejar la seleccion en aleatorio.</p>
        </div>

        @if($grupos->isEmpty())
            <div class="tarjeta-reserva-vacia">
                <h3>No encontramos profesionales disponibles</h3>
                <p>En este momento no hay personal asignado para los servicios seleccionados.</p>
                <a href="{{ route('inicio') }}#servicios" class="boton boton-primario">Cambiar servicios</a>
            </div>
        @else
            <form action="{{ route('cliente.citas.agendar.trabajador.guardar') }}" method="POST" id="form-seleccion-profesionales">
                @csrf
                <div class="acciones-profesionales-limpias">
                    <button type="submit" name="accion" value="aleatorio" class="boton boton-aleatorio">Elegir aleatorio</button>
                </div>

                <div class="bloques-areas-profesionales">
                    @foreach($grupos as $grupo)
                        <section class="bloque-area-profesional">
                            <div class="encabezado-area-profesional limpio">
                                <div>
                                    <h3>Estilistas del área de {{ strtolower($grupo['nombre']) }}</h3>
                                    <p><strong>Servicio:</strong> {{ collect($grupo['servicios'])->pluck('nombre_servicio')->join(', ') }}</p>
                                </div>
                            </div>

                            <div class="rejilla-trabajadores rejilla-trabajadores-compacta">
                                @foreach($grupo['trabajadores'] as $trabajador)
                                    @php $checked = data_get($reserva, 'trabajadores.' . $grupo['clave']) == $trabajador->id_trabajador; @endphp
                                    <label class="tarjeta-trabajador tarjeta-trabajador-opcion {{ $checked ? 'seleccionado' : '' }}" data-grupo="{{ $grupo['clave'] }}">
                                        <input type="radio" name="trabajadores[{{ $grupo['clave'] }}]" value="{{ $trabajador->id_trabajador }}" {{ $checked ? 'checked' : '' }}>
                                        <div class="imagen-trabajador">
                                            <img src="{{ asset('images/services/' . ($trabajador->foto ?? 'default-service.jpg')) }}" alt="{{ $trabajador->nombre_completo }}" onerror="this.onerror=null;this.src='{{ asset('images/services/default-service.jpg') }}';">
                                        </div>
                                        <div class="contenido-trabajador">
                                            <h3>{{ $trabajador->nombre_completo }}</h3>
                                            <p>{{ $trabajador->anios_experiencia }} años de experiencia</p>
                                        </div>
                                        <span class="boton-seleccion-servicio texto-profesional">{{ $checked ? 'Seleccionado ✓' : 'Seleccionar' }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>

                <div class="acciones-reserva-final">
                    <a href="{{ route('inicio') }}#servicios" class="boton boton-secundario">Volver</a>
                    <button type="submit" name="accion" value="manual" class="boton boton-primario">Continuar</button>
                </div>
            </form>
        @endif
    </div>
</section>
@endsection
