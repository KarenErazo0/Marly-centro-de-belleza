@extends('plantillas.app')

@section('title', 'Seleccionar servicios | Marly Centro de Belleza')

@section('content')
<section class="seccion-reservas">
    <div class="contenedor contenedor-reserva">
        <div class="pasos-reserva">
            <span class="paso-reserva activo">1. Servicios</span>
            <span class="paso-reserva">2. Profesional</span>
            <span class="paso-reserva">3. Fecha y hora</span>
            <span class="paso-reserva">4. Confirmación</span>
        </div>

        <div class="encabezado-seccion reserva-centro">
            <span class="contador-servicios">{{ count($reserva['servicios'] ?? []) }} servicios seleccionados</span>
            <h1>Nuestros Servicios</h1>
            <p>Selecciona uno o más servicios que deseas reservar.</p>
        </div>

        <form action="{{ route('cliente.citas.agendar.servicios.guardar') }}" method="POST">
            @csrf
            <div class="rejilla-servicios-reserva">
                @foreach($servicios as $servicio)
                    @php $seleccionado = in_array($servicio->id_servicio, $reserva['servicios'] ?? []); @endphp
                    <label class="tarjeta-servicio-reserva {{ $seleccionado ? 'seleccionado' : '' }}">
                        <input type="checkbox" name="servicios[]" value="{{ $servicio->id_servicio }}" {{ $seleccionado ? 'checked' : '' }}>
                        <div class="caja-imagen-servicio-reserva">
                            <img
                                src="{{ asset('images/services/' . ($servicio->imagen ?? 'default-service.jpg')) }}"
                                alt="{{ $servicio->nombre_servicio }}"
                                onerror="this.onerror=null;this.src='{{ asset('images/services/default-service.jpg') }}';"
                            >
                        </div>
                        <div class="contenido-servicio-reserva">
                            <div class="meta-servicio-reserva">
                                <span class="precio-servicio">${{ number_format($servicio->precio, 0, ',', '.') }}</span>
                                <span class="tiempo-servicio">{{ $servicio->duracion_minutos }} min</span>
                            </div>
                            <h3>{{ $servicio->nombre_servicio }}</h3>
                            <p>{{ $servicio->descripcion }}</p>
                        </div>
                        <span class="boton-seleccion-servicio">{{ $seleccionado ? 'Seleccionado ✓' : 'Seleccionar' }}</span>
                    </label>
                @endforeach
            </div>

            <div class="acciones-reserva-final">
                <a href="{{ route('cliente.citas.index') }}" class="boton boton-secundario">Volver</a>
                <button type="submit" class="boton boton-primario">Continuar con la reserva</button>
            </div>
        </form>
    </div>
</section>
@endsection
