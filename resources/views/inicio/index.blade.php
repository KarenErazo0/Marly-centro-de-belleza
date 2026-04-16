@extends('plantillas.app')

@section('title', 'Inicio | Marly Centro de Belleza')

@section('content')
<section class="seccion-hero">
    <div class="contenedor rejilla-hero">
        <div class="hero-contenido">
            <span class="etiqueta">Belleza · cuidado · estilo</span>
            <h1>Conoce nuestros servicios y agenda tu cita en minutos</h1>
            <p>
                Consulta el catálogo del salón, selecciona uno o más servicios desde esta misma pantalla y continúa con la reserva paso a paso.
            </p>
            <div class="acciones-hero">
                <a class="boton boton-primario" href="#servicios">Ver servicios</a>
                @if(session('cliente_id'))
                    <a class="boton boton-secundario" href="#servicios">Reservar ahora</a>
                @else
                    <a class="boton boton-secundario" href="{{ route('cliente.registro') }}">Crear cuenta</a>
                @endif
            </div>
        </div>
        <div class="tarjeta-hero">
            <h3>Servicios Destacados</h3>
            <ul>
                <li>Peinados, manicure, maquillaje, tintes, pedicure y más.</li>
                <li>Atención profesional y disponibilidad por horario.</li>
                <li>Selección de profesional o asignación aleatoria.</li>
                <li>Confirmación completa de la cita.</li>
            </ul>
        </div>
    </div>
</section>

<section id="servicios" class="seccion-servicios">
    <div class="contenedor">
        <div class="encabezado-seccion">
            <span class="etiqueta">Catálogo</span>
            <h2>Nuestros servicios</h2>
            <p>
                @if(session('cliente_id'))
                    Selecciona uno o más servicios directamente aquí para continuar con tu reserva.
                @else
                    Consulta nombre, descripción, precio y duración aproximada.
                @endif
            </p>
        </div>

        @if(session('cliente_id'))
            <form action="{{ route('cliente.citas.agendar.servicios.guardar') }}" method="POST" id="form-seleccion-servicios">
                @csrf

                <div class="barra-reserva-servicios">
                    <span class="contador-servicios" id="contador-servicios-home">{{ count($reserva['servicios'] ?? []) }} servicios seleccionados</span>
                    <button type="submit" class="boton boton-primario">Continuar con la reserva</button>
                </div>

                <div class="rejilla-servicios rejilla-servicios-home">
                    @forelse($servicios as $servicio)
                        @php $seleccionado = in_array($servicio->id_servicio, $reserva['servicios'] ?? []); @endphp
                        <label class="tarjeta-servicio tarjeta-servicio-home {{ $seleccionado ? 'seleccionado' : '' }}">
                            <input
                                class="input-servicio-home"
                                type="checkbox"
                                name="servicios[]"
                                value="{{ $servicio->id_servicio }}"
                                {{ $seleccionado ? 'checked' : '' }}
                            >

                            <div class="marca-seleccion-servicio">✓</div>

                            <div class="caja-imagen-servicio">
                                <img
                                    src="{{ asset('images/services/' . ($servicio->imagen ?? 'default-service.jpg')) }}"
                                    alt="{{ $servicio->nombre_servicio }}"
                                    class="imagen-servicio"
                                    onerror="this.onerror=null;this.src='{{ asset('images/services/default-service.jpg') }}';"
                                >
                            </div>

                            <div class="contenido-servicio">
                                <div class="meta-servicio">
                                    <span class="precio-servicio">${{ number_format($servicio->precio, 0, ',', '.') }}</span>
                                    <span class="tiempo-servicio">{{ $servicio->duracion_minutos }} min</span>
                                </div>
                                <h3>{{ $servicio->nombre_servicio }}</h3>
                                <p>{{ $servicio->descripcion }}</p>
                                <span class="boton-servicio boton-servicio-home texto-boton-servicio">{{ $seleccionado ? 'Seleccionado ✓' : 'Seleccionar servicio' }}</span>
                            </div>
                        </label>
                    @empty
                        <div class="estado-vacio">
                            <h3>No hay servicios registrados</h3>
                            <p>Ejecuta las migraciones y el seeder para cargar el catálogo inicial.</p>
                        </div>
                    @endforelse
                </div>

                <div class="acciones-home-reserva">
                    <button type="submit" class="boton boton-primario">Continuar con la reserva</button>
                </div>
            </form>
        @else
            <div class="rejilla-servicios">
                @forelse($servicios as $servicio)
                    <article class="tarjeta-servicio">
                        <div class="caja-imagen-servicio">
                            <img
                                src="{{ asset('images/services/' . ($servicio->imagen ?? 'default-service.jpg')) }}"
                                alt="{{ $servicio->nombre_servicio }}"
                                class="imagen-servicio"
                                onerror="this.onerror=null;this.src='{{ asset('images/services/default-service.jpg') }}';"
                            >
                        </div>

                        <div class="contenido-servicio">
                            <div class="meta-servicio">
                                <span class="precio-servicio">${{ number_format($servicio->precio, 0, ',', '.') }}</span>
                                <span class="tiempo-servicio">{{ $servicio->duracion_minutos }} min</span>
                            </div>
                            <h3>{{ $servicio->nombre_servicio }}</h3>
                            <p>{{ $servicio->descripcion }}</p>
                            <a href="{{ route('cliente.registro') }}" class="boton-servicio">Solicitar este servicio <span>→</span></a>
                        </div>
                    </article>
                @empty
                    <div class="estado-vacio">
                        <h3>No hay servicios registrados</h3>
                        <p>Ejecuta las migraciones y el seeder para cargar el catálogo inicial.</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</section>

<section id="contacto" class="seccion-informacion">
    <div class="contenedor rejilla-informacion">
        <div class="tarjeta-informacion">
            <h3>Reserva tu cita</h3>
            <p>Selecciona uno o más servicios, elige el profesional disponible y confirma tu reserva en línea.</p>
            <a href="{{ session('cliente_id') ? route('inicio') . '#servicios' : route('cliente.registro') }}" class="boton boton-primario">{{ session('cliente_id') ? 'Empezar reserva' : 'Ir al registro' }}</a>
        </div>
        <div class="tarjeta-informacion">
            <h3>Contacto y horarios</h3>
            <p><strong>Ubicación:</strong> Pasto, Nariño</p>
            <p><strong>Teléfono:</strong> 315 000 0000</p>
            <p><strong>Correo:</strong> marly@centrobelleza.com</p>
            <p><strong>Lunes a viernes:</strong> 7:00 a.m. - 7:00 p.m.</p>
            <p><strong>Sábados:</strong> 8:00 a.m. - 7:00 p.m.</p>
        </div>
    </div>
</section>
@endsection
