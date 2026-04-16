@extends('plantillas.app')

@section('title', 'Confirmar reserva | Marly Centro de Belleza')

@section('content')
<section class="seccion-reservas">
    <div class="contenedor contenedor-reserva contenedor-confirmacion">
        <div class="pasos-reserva">
            <span class="paso-reserva completado">1. Servicios</span>
            <span class="paso-reserva completado">2. Profesional</span>
            <span class="paso-reserva completado">3. Fecha y hora</span>
            <span class="paso-reserva activo">4. Confirmación</span>
        </div>

        <div class="encabezado-seccion reserva-centro">
            <h1>Completa tu reserva</h1>
            <p>Solo faltan algunos datos para confirmar tu cita.</p>
        </div>

        <form action="{{ route('cliente.citas.agendar.registrar') }}" method="POST" class="tarjeta-confirmacion-reserva">
            @csrf
            <div class="resumen-cita-final">
                <h3>Resumen de tu cita:</h3>
                <div class="chips-reserva">
                    @foreach($servicios as $servicio)
                        <span class="chip-servicio">{{ $servicio->nombre_servicio }}</span>
                    @endforeach
                </div>

                <div class="rejilla-resumen-final">
                    <div>
                        <span>Profesionales</span>
                        @foreach($gruposSeleccionados as $grupo)
                            <strong>{{ $grupo['nombre'] }}: {{ $grupo['trabajador']->nombre_completo }}</strong>
                            <small>{{ $grupo['trabajador']->especialidad }}</small>
                        @endforeach
                    </div>
                    <div>
                        <span>Fecha</span>
                        <strong>{{ \Carbon\Carbon::parse($reserva['fecha'])->translatedFormat('l, j \d\e F \d\e Y') }}</strong>
                    </div>
                    <div>
                        <span>Hora</span>
                        <strong>{{ \Carbon\Carbon::createFromFormat('H:i', $reserva['hora'])->translatedFormat('h:i A') }}</strong>
                    </div>
                    <div>
                        <span>Duración estimada</span>
                        <strong>{{ $duracionTotal }} minutos</strong>
                    </div>
                </div>
            </div>

            <div class="grupo-campo">
                <label for="nombre_cliente">Nombre completo *</label>
                <input type="text" id="nombre_cliente" name="nombre_cliente" value="{{ old('nombre_cliente', $cliente->nombre_completo) }}" required>
            </div>

            <div class="grupo-campo">
                <label for="telefono_contacto">Teléfono *</label>
                <input type="text" id="telefono_contacto" name="telefono_contacto" value="{{ old('telefono_contacto', $cliente->telefono) }}" required>
            </div>

            <div class="grupo-campo">
                <label for="notas">Notas adicionales (opcional)</label>
                <textarea id="notas" name="notas" rows="4" placeholder="¿Alguna preferencia o solicitud especial?">{{ old('notas') }}</textarea>
            </div>

            <div class="acciones-reserva-final">
                <a href="{{ route('cliente.citas.agendar.horario') }}" class="boton boton-secundario">Volver</a>
                <button type="submit" class="boton boton-primario">Confirmar reserva</button>
            </div>
        </form>
    </div>
</section>
@endsection
