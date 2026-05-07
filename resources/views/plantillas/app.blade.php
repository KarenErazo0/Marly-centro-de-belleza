<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Marly Centro de Belleza')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])


</head>

<body>
    <header class="encabezado-sitio">
        <div class="contenedor barra-navegacion">
            <a class="marca" href="{{ route('inicio') }}">
                <img class="marca-logo" src="{{ asset('images/logo-marly.png') }}" alt="Logo Marly Centro de Belleza">
                <span>Marly Centro de Belleza</span>
            </a>

            <nav class="enlaces-navegacion">
                <a href="{{ route('inicio') }}#servicios">
                    <span class="nav-icono-inline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 20 20 4"></path>
                            <path d="M9 4 4 9"></path>
                            <path d="M15 20l5-5"></path>
                        </svg>
                    </span>
                    Servicios
                </a>

                <a href="{{ route('inicio') }}#contacto">
                    <span class="nav-icono-inline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07A19.5 19.5 0 0 1 5.15 12.8 19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.77.62 2.61a2 2 0 0 1-.45 2.11L8 9.67a16 16 0 0 0 6.33 6.33l1.23-1.23a2 2 0 0 1 2.11-.45c.84.29 1.71.5 2.61.62A2 2 0 0 1 22 16.92Z"></path>
                        </svg>
                    </span>
                    Contacto
                </a>

                @if(session('admin_autenticado'))
                    <a href="{{ route('admin.dashboard') }}">Panel admin</a>

                    <form action="{{ route('cliente.salir') }}" method="POST">
                        @csrf
                        <button type="submit" class="boton-enlace">Cerrar sesión</button>
                    </form>
                @elseif(session('cliente_id'))
    @php
        $nombreCliente = session('cliente_nombre', 'Cliente');
        $inicialCliente = mb_strtoupper(mb_substr($nombreCliente, 0, 1));
    @endphp

    <a href="{{ route('cliente.cuenta') }}">Mi cuenta</a>

    <div class="marly-user-menu" data-user-menu>
        <button type="button" class="marly-user-button" data-user-menu-button aria-label="Abrir menú de usuario">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21a8 8 0 0 0-16 0"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </button>

        <div class="marly-user-dropdown">
            <div class="marly-user-info">
                <div class="marly-user-avatar">{{ $inicialCliente }}</div>
                <div>
                    <strong>{{ $nombreCliente }}</strong>
                    <span>Cliente registrado</span>
                </div>
            </div>

            <div class="marly-user-separador"></div>

            <a href="{{ route('cliente.cuenta') }}" class="marly-user-option">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21a8 8 0 0 0-16 0"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Mi cuenta
            </a>

            <div class="marly-user-separador"></div>

            <form action="{{ route('cliente.salir') }}" method="POST" class="marly-user-logout-form">
                @csrf
                <button type="submit" class="marly-user-option marly-user-logout">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <path d="M10 17l5-5-5-5"></path>
                        <path d="M15 12H3"></path>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
                @else
                    <a href="{{ route('cliente.ingresar') }}">
                        <span class="nav-icono-inline">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21a8 8 0 0 0-16 0"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                        Iniciar sesión
                    </a>

                    <a class="boton boton-primario boton-pequeno boton-registro-navbar" href="{{ route('cliente.registro') }}">
                        Registrarme
                        <span>→</span>
                    </a>
                @endif

                <button type="button" class="theme-toggle" id="theme-toggle" aria-label="Cambiar a modo oscuro" title="Cambiar tema">
                    <span class="theme-toggle-track" aria-hidden="true">
                        <span class="theme-toggle-thumb">
                            <svg class="theme-icon theme-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="4"></circle>
                                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
                            </svg>

                            <svg class="theme-icon theme-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 12.8A8.5 8.5 0 1 1 11.2 3 6.7 6.7 0 0 0 21 12.8Z"></path>
                            </svg>
                        </span>
                    </span>
                </button>
            </nav>
        </div>
    </header>

    <main class="contenido-principal">
        <div class="contenedor contenedor-alertas">
            @if(session('success'))
                <div class="alerta alerta-exito">
                    <span class="alerta-icono">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alerta alerta-error">
                    <span class="alerta-icono">!</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alerta alerta-error">
                    <span class="alerta-icono">!</span>
                    <div>
                        <strong>Revisa la información:</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <footer class="pie-sitio">
        <div class="contenedor footer-contenido">
            <div class="footer-columna footer-marca">
                <h4>Marly Centro de Belleza</h4>
                <p>
                    Empresa familiar con más de 40 años de experiencia, dedicada a brindar servicios de belleza
                    con atención personalizada, compromiso y confianza.
                </p>
                <strong>“Belleza, cuidado y confianza en cada detalle.”</strong>
            </div>

            <div class="footer-columna">
                <h4>Enlaces rápidos</h4>
                <nav class="footer-enlaces">
                    <a href="{{ route('inicio') }}">Inicio</a>
                    <a href="{{ route('inicio') }}#servicios">Servicios</a>
                    <a href="{{ route('inicio') }}#sobre-nosotros">Sobre nosotros</a>
                    <a href="{{ route('inicio') }}#contacto">Contacto</a>
                </nav>
            </div>

            <div class="footer-columna">
                <h4>Nuestras redes sociales</h4>
                <div class="footer-redes">
                    <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer">Instagram</a>
                    <a href="https://wa.me/573000000000" target="_blank" rel="noopener noreferrer">WhatsApp</a>
                </div>
            </div>
        </div>

        <div class="contenedor footer-inferior">
            <p>© 2026 Marly Centro de Belleza. Todos los derechos reservados.</p>
            <p class="marca-autores">Desarrollado por Karen Erazo y Johan Serrano.</p>
        </div>
    </footer>
</body>
</html>