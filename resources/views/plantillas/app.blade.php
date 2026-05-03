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
                <a href="{{ route('inicio') }}#servicios">Servicios</a>
                <a href="{{ route('inicio') }}#sobre-nosotros">Contacto</a>
                <button type="button" class="theme-toggle" id="theme-toggle" aria-label="Cambiar a modo oscuro" title="Cambiar tema">
                    <span class="theme-toggle-track" aria-hidden="true">
                        <span class="theme-toggle-thumb">
                            <svg class="theme-icon theme-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="4"></circle>
                                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
                            </svg>
                            <svg class="theme-icon theme-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.8A8.5 8.5 0 1 1 11.2 3 6.7 6.7 0 0 0 21 12.8Z"></path>
                            </svg>
                        </span>
                    </span>
                </button>
                @if(session('admin_autenticado'))
                    <a href="{{ route('admin.dashboard') }}">Panel admin</a>
                    <form action="{{ route('cliente.salir') }}" method="POST">
                        @csrf
                        <button type="submit" class="boton-enlace">Cerrar sesión</button>
                    </form>
                @elseif(session('cliente_id'))
                    <a href="{{ route('cliente.cuenta') }}">Mi cuenta</a>
                    <form action="{{ route('cliente.salir') }}" method="POST">
                        @csrf
                        <button type="submit" class="boton-enlace">Cerrar sesión</button>
                    </form>
                @else
                    <a href="{{ route('cliente.ingresar') }}">Iniciar sesión</a>
                    <a class="boton boton-primario boton-pequeno" href="{{ route('cliente.registro') }}">Registrarme</a>
                @endif
            </nav>
        </div>
    </header>

    <main class="contenido-principal">
        <div class="contenedor contenedor-alertas">
            @if(session('success'))
                <div class="alerta alerta-exito"><span class="alerta-icono">✓</span><span>{{ session('success') }}</span></div>
            @endif
            @if(session('error'))
                <div class="alerta alerta-error"><span class="alerta-icono">!</span><span>{{ session('error') }}</span></div>
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

    <footer class="pie-sitio footer-premium">
        <div class="contenedor footer-contenido">
            <div class="footer-columna footer-marca">
                <h4>Marly Centro de Belleza</h4>
                <p>Empresa familiar con más de 40 años de experiencia, dedicada a brindar servicios de belleza con atención personalizada, compromiso y confianza.</p>
                <strong>“Belleza, cuidado y confianza en cada detalle.”</strong>
            </div>

            <div class="footer-columna">
                <h4>Enlaces rápidos</h4>
                <nav class="footer-enlaces" aria-label="Enlaces rápidos del footer">
                    <a href="{{ route('inicio') }}">Inicio</a>
                    <a href="{{ route('inicio') }}#servicios">Servicios</a>
                    <a href="{{ route('inicio') }}#sobre-nosotros">Sobre nosotros</a>
                    <a href="{{ route('inicio') }}#sobre-nosotros">Contacto</a>
                    <a href="{{ route('cliente.citas.agendar.servicios') }}">Agendar cita</a>
                </nav>
            </div>

            <div class="footer-columna">
                <h4>Nuestras redes sociales</h4>
                <div class="footer-redes">
                    <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagram Marly Centro de Belleza">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                            <circle cx="12" cy="12" r="4"></circle>
                            <circle cx="17.5" cy="6.5" r=".8" fill="currentColor" stroke="none"></circle>
                        </svg>
                        <span>Instagram</span>
                    </a>
                    <a href="https://wa.me/573000000000" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp Marly Centro de Belleza">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 11.7a8 8 0 0 1-11.8 7l-4.2 1.1 1.1-4.1A8 8 0 1 1 20 11.7Z"></path>
                            <path d="M9.4 8.5c.2-.4.4-.5.7-.5h.5c.2 0 .4.1.5.4l.7 1.5c.1.3.1.5-.1.7l-.4.5c.6 1.1 1.5 2 2.7 2.6l.5-.4c.2-.2.5-.2.7-.1l1.5.7c.3.1.4.3.4.6v.5c0 .3-.2.6-.5.7-.6.3-1.4.4-2.4.1-2.5-.7-5.1-3.2-5.8-5.8-.3-.9-.2-1.7.1-2.3Z"></path>
                        </svg>
                        <span>WhatsApp</span>
                    </a>
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
