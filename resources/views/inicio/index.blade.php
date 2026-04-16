@extends('plantillas.app')

@section('title', 'Inicio | Marly Centro de Belleza')

@section('content')
<section class="seccion-hero">
    <div class="contenedor rejilla-hero">
        <div class="hero-contenido">
            <span class="etiqueta">Belleza · cuidado · estilo</span>
            <h1>Conoce nuestros servicios y crea tu cuenta en minutos</h1>
            <p>
                Consulta el catálogo del salón de forma clara, descubre los tratamientos disponibles
                y accede a tu cuenta para gestionar tu información de cliente.
            </p>
            <div class="acciones-hero">
                <a class="boton boton-primario" href="#servicios">Ver servicios</a>
                @if(session('cliente_id'))
                    <a class="boton boton-secundario" href="{{ route('cliente.cuenta') }}">Ir a mi cuenta</a>
                @else
                    <a class="boton boton-secundario" href="{{ route('cliente.registro') }}">Crear cuenta</a>
                @endif
            </div>
        </div>
        <div class="tarjeta-hero">
            <h3>Servicios Destacados</h3>
            <ul>
                <li>Peinados, Manicure, Maquillaje, Tintes, Pedicure y más.</li>
                <li>-Atención profesional.</li>
                <li>-Ambiente agradable.</li>
                <li>-Catalogo claro y accesible.</li>
            </ul>
        </div>
    </div>
</section>

<section id="servicios" class="seccion-servicios">
    <div class="contenedor">
        <div class="encabezado-seccion">
            <span class="etiqueta">Catálogo</span>
            <h2>Nuestros servicios</h2>
            <p>Consulta nombre, descripción, precio y duración aproximada.</p>
        </div>

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
    </div>
</section>

<section id="contacto" class="seccion-informacion">
    <div class="contenedor rejilla-informacion">
        <div class="tarjeta-informacion">
            <h3>Registro de cliente</h3>
            <p>Crea tu cuenta con tu correo electrónico para acceder a tu panel y gestionar tu información personal.</p>
            <a href="{{ route('cliente.registro') }}" class="boton boton-primario">Ir al registro</a>
        </div>
        <div class="tarjeta-informacion">
            <h3>Contacto y horarios</h3>
            <p><strong>Ubicación:</strong> Pasto, Nariño</p>
            <p><strong>Teléfono:</strong> 315 000 0000</p>
            <p><strong>Correo:</strong> marly@centrobelleza.com</p>
            <p><strong>Lunes a viernes:</strong> 7:00 a.m. - 8:00 p.m.</p>
            <p><strong>Sábados y festivos:</strong> 8:00 a.m. - 7:00 p.m.</p>
        </div>
    </div>
</section>
@endsection
