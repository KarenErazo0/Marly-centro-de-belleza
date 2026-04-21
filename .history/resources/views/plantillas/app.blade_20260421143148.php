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
            <a class="marca" href="{{ route('inicio') }}">Marly Centro de Belleza</a>
            <nav class="enlaces-navegacion">
                <a href="{{ route('inicio') }}#servicios">Servicios</a>
                <a href="{{ route('inicio') }}#contacto">Contacto</a>
                @if(session('cliente_id'))
                    <a href="{{ route('cliente.citas.index') }}">Mis citas</a>
                @endif
                @if(session('cliente_id'))
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
        <div class="contenedor">
            @if(session('success'))
                <div class="alerta alerta-exito">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alerta alerta-error">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alerta alerta-error">
                    <strong>Revisa la información:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        @yield('content')
    </main>

    <footer class="pie-sitio">
        <div class="contenedor rejilla-footer">
            <div>
                <h4>Marly Centro de Belleza</h4>
                <p>Empresa familiar con mas de 40 años de experiencia, dedicada a brindar servicios de belleza con atención personalizada,   se caracteriza por su estabilidad y, compromiso con la satisfacción del cliente y la confianza que ha construido a lo largo del tiempo. </p>
            </div>
            <div>
                <h4>Horarios</h4>
                <p>Lunes a viernes: 7:00 a.m. - 7:00 p.m.</p>
                <p>Sábados: 8:00 a.m. - 7:00 p.m.</p>
            </div>
            <div>
                <h4>Contacto</h4>
                <p>Pasto, Nariño</p>
                <p>315 000 0000</p>
                <p>marly@centrobelleza.com</p>
            </div>
        </div>
    </footer>
</body>
</html>
