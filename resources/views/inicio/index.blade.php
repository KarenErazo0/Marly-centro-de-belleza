@extends('plantillas.app')

@section('title', 'Inicio | Marly Centro de Belleza')

@section('content')
<section class="seccion-hero">
    <div class="contenedor rejilla-hero">
        <div class="hero-contenido">
            <span class="etiqueta">Belleza · cuidado · estilo</span>
            <h1>Realza tu belleza en manos expertas</h1>
            <p>En Marly Centro de Belleza creamos experiencias únicas para ti con atención cálida, profesional y personalizada.</p>

            <div class="hero-agenda">
                <h2>Agenda tu cita</h2>
                <p>Selecciona el servicio, elige tu profesional y reserva en el horario que mejor se adapte a ti.</p>
                <div class="acciones-hero">
                    <a class="boton boton-primario" href="#servicios">Ver servicios</a>
                    @if(session('cliente_id'))
                        <a class="boton boton-secundario" href="#servicios">Reservar ahora</a>
                    @else
                        <a class="boton boton-secundario" href="{{ route('cliente.registro') }}">Crear cuenta</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="hero-imagen-salon hero-imagen-circular">
            <img src="{{ asset('images/services/default-service.jpg') }}" alt="Salón Marly Centro de Belleza" onerror="this.onerror=null;this.src='{{ asset('images/services/manicure.jpg') }}';">
        </div>
    </div>
</section>

<section id="servicios" class="seccion-servicios">
    <div class="contenedor">
        <div class="encabezado-seccion">
            <h2>Nuestros servicios</h2>
            <p>Explora nuestra variedad de servicios diseñados para tu cuidado personal.</p>
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
                            <input class="input-servicio-home" type="checkbox" name="servicios[]" value="{{ $servicio->id_servicio }}" {{ $seleccionado ? 'checked' : '' }}>
                            <div class="marca-seleccion-servicio">✓</div>
                            <div class="caja-imagen-servicio">
                                <img src="{{ asset('images/services/' . ($servicio->imagen ?? 'default-service.jpg')) }}" alt="{{ $servicio->nombre_servicio }}" class="imagen-servicio" onerror="this.onerror=null;this.src='{{ asset('images/services/default-service.jpg') }}';">
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
                            <img src="{{ asset('images/services/' . ($servicio->imagen ?? 'default-service.jpg')) }}" alt="{{ $servicio->nombre_servicio }}" class="imagen-servicio" onerror="this.onerror=null;this.src='{{ asset('images/services/default-service.jpg') }}';">
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

<section id="sobre-nosotros" class="seccion-sobre-nosotros">
    <div class="contenedor">
        <div class="encabezado-seccion encabezado-sobre-nosotros">
            <span class="etiqueta">Sobre nosotros</span>
        </div>

        <div class="layout-sobre-nosotros">
            <div class="carrusel-sobre" aria-label="Carrusel de fotos de Marly Centro de Belleza">
                <div class="carrusel-ventana" id="carrusel-sobre-ventana">
                    @foreach(['manicure.jpg', 'pedicure.jpg', 'peinado.jpg', 'tinte.jpg', 'maquillaje.jpg', 'depilacion.jpg'] as $foto)
                        <div class="slide-sobre">
                            <img src="{{ asset('images/services/' . $foto) }}" alt="Foto de Marly Centro de Belleza">
                        </div>
                    @endforeach
                </div>
                <button class="control-carrusel control-prev" type="button" data-carousel-prev aria-label="Foto anterior">‹</button>
                <button class="control-carrusel control-next" type="button" data-carousel-next aria-label="Foto siguiente">›</button>
            </div>

            <div class="bloque-mision-vision">
                <article class="tarjeta-mision">
                    <h3>Misión</h3>
                    <p>Marly Centro de Belleza es un centro de estética con amplia trayectoria y experiencia, dedicado a ofrecer servicios de belleza con calidad, calidez y atención personalizada, creando un ambiente acogedor donde cada cliente se sienta valorado, respetado e incluido.</p>
                </article>
                <article class="tarjeta-mision">
                    <h3>Visión</h3>
                    <p>Mantener a Marly Centro de Belleza como una empresa sólida y reconocida en el sector de la belleza, destacándose por su experiencia, ambiente acogedor y trato cercano, adaptándose a las nuevas tendencias sin perder su identidad y esencia familiar.</p>
                </article>
            </div>
        </div>
    </div>
</section>

<section id="contacto" class="seccion-informacion">
    <div class="contenedor rejilla-contacto-mapa">
        <div class="tarjeta-informacion tarjeta-contacto-principal">
            <span class="etiqueta">Contacto y horarios</span>
            <p><strong>Ubicación:</strong> Pasto, Nariño</p>
            <p><strong>Teléfono:</strong> 7291317</p>
            <p><strong>Correo:</strong> marly@centrobelleza.com</p>
            <p><strong>Horario:</strong> lunes a viernes de 7:00 a.m. a 7:00 p.m. y sábados y festivos de 8:00 a.m. a 7:00 p.m.</p>
            <div class="redes-contacto">
                <span class="titulo-redes">Nuestras redes sociales:</span>
                <a href="https://www.instagram.com/marly.salon?igsh=eGhtNTZscnZ1cnR3" target="_blank" rel="noopener" aria-label="Instagram de Marly Centro de Belleza">
                    <svg class="icono-red-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                        <circle cx="12" cy="12" r="4"></circle>
                        <circle cx="17.5" cy="6.5" r=".8" fill="currentColor" stroke="none"></circle>
                    </svg> Instagram
                </a>
                <a href="https://wa.link/rsduzp" target="_blank" rel="noopener" aria-label="WhatsApp de Marly Centro de Belleza">
                    <svg class="icono-red-svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12.04 2.01A9.84 9.84 0 0 0 3.6 16.9L2.3 21.7l4.93-1.29a9.82 9.82 0 0 0 4.8 1.23h.01a9.82 9.82 0 0 0 0-19.63Zm0 17.96h-.01a8.16 8.16 0 0 1-4.16-1.14l-.3-.18-2.93.77.78-2.85-.2-.3A8.16 8.16 0 1 1 12.04 19.97Zm4.47-6.1c-.24-.12-1.45-.72-1.67-.8-.22-.08-.38-.12-.54.12-.16.24-.62.8-.76.96-.14.16-.28.18-.52.06-.24-.12-1.02-.38-1.95-1.2-.72-.64-1.2-1.43-1.35-1.67-.14-.24-.02-.37.1-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.2-.47-.39-.4-.54-.41h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.6 4.12 3.64.58.25 1.03.4 1.38.51.58.18 1.1.16 1.52.1.46-.07 1.45-.59 1.65-1.16.2-.57.2-1.06.14-1.16-.06-.1-.22-.16-.46-.28Z"></path>
                    </svg> WhatsApp
                </a>
            </div>
        </div>

        <div class="tarjeta-mapa">
            <iframe title="Ubicación Marly Centro de Belleza" src="https://www.google.com/maps?q=Marly%20Centro%20de%20Belleza%20Pasto%20Nari%C3%B1o&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>
@endsection
