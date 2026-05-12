@extends('plantillas.app')

@section('title', 'Inicio | Marly Centro de Belleza')

@section('content')
    <section class="seccion-hero marly-home-hero-exacto">
        <div class="contenedor rejilla-hero marly-home-hero-grid">
            <div class="hero-contenido marly-home-hero-contenido">
                <span class="etiqueta marly-home-hero-etiqueta">Belleza · cuidado · estilo</span>

                <h1>Realza tu belleza en manos expertas</h1>

                <p>En Marly Centro de Belleza creamos experiencias únicas para ti con atención cálida, profesional y
                    personalizada.</p>

                <div class="hero-agenda marly-home-agenda-card">
                    <h2 class="marly-home-agenda-titulo">
                        <span class="marly-home-icono-calendario" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="17" rx="2"></rect>
                                <path d="M16 2v4"></path>
                                <path d="M8 2v4"></path>
                                <path d="M3 10h18"></path>
                                <path d="M8 14h3"></path>
                                <path d="M8 17h6"></path>
                            </svg>
                        </span>
                        Agenda tu cita
                    </h2>

                    <p>Selecciona el servicio, elige tu profesional y reserva en el horario que mejor se adapte a ti.</p>

                    <div class="acciones-hero marly-home-agenda-acciones">
                        <a href="{{ route('inicio') }}#servicios" class="boton boton-primario marly-home-btn-hero">
                            <span>Ver servicios</span>
                            <span class="marly-home-icono-flecha">→</span>
                        </a>

                        <a href="{{ route('cliente.registro') }}" class="boton boton-secundario marly-home-btn-hero">
                            <span>Crear cuenta</span>
                            <span class="marly-home-icono-usuario" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="8.5" cy="7" r="4"></circle>
                                    <path d="M20 8v6"></path>
                                    <path d="M23 11h-6"></path>
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>

            @php
                $heroImagen = $configuracion->hero_imagen ?: 'default-service.jpg';
                $heroCarpeta = str_starts_with($heroImagen, 'hero-admin-') ? 'site' : 'services';
            @endphp

            <div class="hero-imagen-salon hero-imagen-circular marly-home-hero-imagen">
                <img src="{{ str_starts_with($heroImagen, 'http') ? $heroImagen : asset('images/' . $heroCarpeta . '/' . $heroImagen) }}"
                    alt="Salón Marly Centro de Belleza"
                    onerror="this.onerror=null;this.src='{{ asset('images/services/default-service.jpg') }}';">
            </div>
        </div>
    </section>

    <section id="servicios" class="seccion-servicios">
        <div class="contenedor">
            <div class="encabezado-seccion">
                <h2>Nuestros servicios</h2>
                <p>Explora nuestra variedad de servicios diseñados para tu cuidado personal.</p>
            </div>

            @if (session('cliente_id'))
                <form action="{{ route('cliente.citas.agendar.servicios.guardar') }}" method="POST"
                    id="form-seleccion-servicios">
                    @csrf

                    <div class="barra-reserva-servicios">
                        <span class="contador-servicios"
                            id="contador-servicios-home">{{ count($reserva['servicios'] ?? []) }} servicios
                            seleccionados</span>
                        <button type="submit" class="boton boton-primario">Continuar con la reserva</button>
                    </div>

                    <div class="rejilla-servicios rejilla-servicios-home">
                        @forelse($servicios as $servicio)
                            @php $seleccionado = in_array($servicio->id_servicio, $reserva['servicios'] ?? []); @endphp
                            <label class="tarjeta-servicio tarjeta-servicio-home {{ $seleccionado ? 'seleccionado' : '' }}">
                                <input class="input-servicio-home" type="checkbox" name="servicios[]"
                                    value="{{ $servicio->id_servicio }}" {{ $seleccionado ? 'checked' : '' }}>
                                <div class="marca-seleccion-servicio">✓</div>
                                @php
                                    $imagenServicio = $servicio->imagen ?? 'default-service.jpg';
                                @endphp
                                <div class="caja-imagen-servicio">
                                    <img src="{{ str_starts_with($imagenServicio, 'http') ? $imagenServicio : asset('images/services/' . $imagenServicio) }}"
                                        alt="{{ $servicio->nombre_servicio }}" class="imagen-servicio"
                                        onerror="this.onerror=null;this.src='{{ asset('images/services/default-service.jpg') }}';">
                                </div>
                                <div class="contenido-servicio">
                                    <div class="meta-servicio">
                                        <span
                                            class="precio-servicio">${{ number_format($servicio->precio, 0, ',', '.') }}</span>
                                        <span class="tiempo-servicio">{{ $servicio->duracion_minutos }} min</span>
                                    </div>
                                    <h3>{{ $servicio->nombre_servicio }}</h3>
                                    <p>{{ $servicio->descripcion }}</p>
                                    <span
                                        class="boton-servicio boton-servicio-home texto-boton-servicio">{{ $seleccionado ? 'Seleccionado ✓' : 'Seleccionar servicio' }}</span>
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
                            @php
                                $imagenServicio = $servicio->imagen ?? 'default-service.jpg';
                            @endphp
                            <div class="caja-imagen-servicio">
                                <img src="{{ str_starts_with($imagenServicio, 'http') ? $imagenServicio : asset('images/services/' . $imagenServicio) }}"
                                    alt="{{ $servicio->nombre_servicio }}" class="imagen-servicio"
                                    onerror="this.onerror=null;this.src='{{ asset('images/services/default-service.jpg') }}';">
                            </div>
                            <div class="contenido-servicio">
                                <div class="meta-servicio">
                                    <span
                                        class="precio-servicio">${{ number_format($servicio->precio, 0, ',', '.') }}</span>
                                    <span class="tiempo-servicio">{{ $servicio->duracion_minutos }} min</span>
                                </div>
                                <h3>{{ $servicio->nombre_servicio }}</h3>
                                <p>{{ $servicio->descripcion }}</p>
                                <a href="{{ route('cliente.registro') }}" class="boton-servicio">Solicitar este servicio
                                    <span>→</span></a>
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

    <section id="sobre-nosotros" class="seccion-sobre-nosotros marly-sobre-exacto">
        <div class="contenedor">
            <div class="layout-sobre-nosotros marly-sobre-layout">
                <div class="carrusel-sobre marly-sobre-carrusel" aria-label="Carrusel de fotos de Marly Centro de Belleza">
                    <div class="carrusel-ventana" id="carrusel-sobre-ventana">
                        @foreach (['manicure.jpg', 'pedicure.jpg', 'peinado.jpg', 'tinte.jpg', 'maquillaje.jpg', 'depilacion.jpg'] as $foto)
                            <div class="slide-sobre">
                                <img src="{{ asset('images/services/' . $foto) }}" alt="Foto de Marly Centro de Belleza">
                            </div>
                        @endforeach
                    </div>

                    <button class="control-carrusel control-prev marly-sobre-control" type="button" data-carousel-prev
                        aria-label="Foto anterior">‹</button>

                    <button class="control-carrusel control-next marly-sobre-control" type="button" data-carousel-next
                        aria-label="Foto siguiente">›</button>

                    <div class="marly-sobre-linea-decorativa"></div>
                </div>

                <div class="bloque-mision-vision marly-sobre-bloque">
                    <article class="tarjeta-mision marly-sobre-card">
                        <div class="marly-sobre-card-header">
                            <span class="marly-sobre-icono">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="8"></circle>
                                    <circle cx="12" cy="12" r="4"></circle>
                                    <path d="M12 2v3"></path>
                                    <path d="M22 12h-3"></path>
                                    <path d="M12 22v-3"></path>
                                    <path d="M2 12h3"></path>
                                </svg>
                            </span>

                            <div>
                                <h3>Misión</h3>
                                <span class="marly-sobre-sublinea"></span>
                            </div>
                        </div>

                        <p>Marly Centro de Belleza es un centro de estética con amplia trayectoria y experiencia, dedicado a
                            ofrecer servicios de belleza con calidad, calidez y atención personalizada, creando un ambiente
                            acogedor donde cada cliente se sienta valorado, respetado e incluido.</p>
                    </article>

                    <article class="tarjeta-mision marly-sobre-card">
                        <div class="marly-sobre-card-header">
                            <span class="marly-sobre-icono">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </span>

                            <div>
                                <h3>Visión</h3>
                                <span class="marly-sobre-sublinea"></span>
                            </div>
                        </div>

                        <p>Mantener a Marly Centro de Belleza como una empresa sólida y reconocida en el sector de la
                            belleza, destacándose por su experiencia, ambiente acogedor y trato cercano, adaptándose a las
                            nuevas tendencias sin perder su identidad y esencia familiar.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section id="contacto" class="seccion-informacion marly-contacto-exacto">
        <div class="contenedor">
            <div class="rejilla-contacto-mapa marly-contacto-layout">
                <article class="tarjeta-informacion tarjeta-contacto-principal marly-contacto-card">
                    <span class="etiqueta marly-contacto-etiqueta">
                        <span class="marly-contacto-etiqueta-icono">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="17" rx="2"></rect>
                                <path d="M16 2v4"></path>
                                <path d="M8 2v4"></path>
                                <path d="M3 10h18"></path>
                            </svg>
                        </span>
                        Contacto y horarios
                    </span>

                    <div class="marly-contacto-lista">
                        <p class="marly-contacto-item">
                            <span class="marly-contacto-icono">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z" />
                                </svg>
                            </span>
                            <span><strong>Ubicación:</strong> Pasto, Nariño</span>
                        </p>

                        <p class="marly-contacto-item">
                            <span class="marly-contacto-icono">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.58a1 1 0 0 1-.24 1.01l-2.21 2.2Z" />
                                </svg>
                            </span>
                            <span><strong>Teléfono:</strong> 7291317</span>
                        </p>

                        <p class="marly-contacto-item">
                            <span class="marly-contacto-icono">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z" />
                                </svg>
                            </span>
                            <span><strong>Correo:</strong> marly@centrobelleza.com</span>
                        </p>

                        <p class="marly-contacto-item">
                            <span class="marly-contacto-icono">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="M12 7v5l3 2"></path>
                                </svg>
                            </span>
                            <span><strong>Horario:</strong> lunes a viernes de 7:00 a.m. a 7:00 p.m. y sábados y festivos de
                                8:00 a.m. a 7:00 p.m.</span>
                        </p>
                    </div>

                    <div class="marly-contacto-separador"></div>

                    <h3 class="marly-contacto-redes-titulo">Nuestras redes sociales:</h3>

                    <div class="redes-contacto marly-contacto-redes">
                        <a href="{{ $configuracion->instagram_url ?? '#' }}" class="marly-contacto-red-btn"
                            target="_blank" rel="noopener noreferrer">
                            <span class="marly-contacto-red-icono">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                                    <circle cx="12" cy="12" r="4"></circle>
                                    <circle cx="17.5" cy="6.5" r="1"></circle>
                                </svg>
                            </span>
                            Instagram
                        </a>

                        <a href="{{ $configuracion->whatsapp_url ?? '#' }}"
    class="marly-contacto-red-btn"
    target="_blank"
    rel="noopener noreferrer">
                            <span class="marly-contacto-red-icono">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.46 0 .09 5.37.09 11.97c0 2.11.55 4.17 1.6 5.98L0 24l6.2-1.63a11.96 11.96 0 0 0 5.86 1.49h.01c6.6 0 11.97-5.37 11.97-11.97 0-3.2-1.25-6.21-3.52-8.41ZM12.07 21.84h-.01a9.9 9.9 0 0 1-5.04-1.38l-.36-.21-3.68.97.98-3.59-.23-.37a9.9 9.9 0 0 1-1.52-5.29c0-5.49 4.47-9.96 9.97-9.96a9.88 9.88 0 0 1 7.04 2.92 9.9 9.9 0 0 1 2.92 7.04c0 5.5-4.47 9.97-9.97 9.97Zm5.46-7.45c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.47-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.61-.92-2.21-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.49s1.07 2.89 1.22 3.09c.15.2 2.11 3.22 5.1 4.51.71.31 1.26.49 1.7.63.71.23 1.36.2 1.87.12.57-.08 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.13-.27-.2-.57-.35Z" />
                                </svg>
                            </span>
                            WhatsApp
                        </a>
                    </div>
                </article>

                <div class="tarjeta-mapa marly-contacto-mapa">
                    <iframe
                        src="https://www.google.com/maps?q=Marly%20Centro%20De%20Belleza%20Pasto%20Nari%C3%B1o&output=embed"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Mapa Marly Centro de Belleza">
                    </iframe>
                </div>
            </div>
        </div>
    </section>
@endsection
