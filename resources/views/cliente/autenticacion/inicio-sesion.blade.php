@extends('plantillas.app')

@section('title', 'Inicio de sesión | Marly Centro de Belleza')

@section('content')
    <section class="seccion-formulario">
        <div class="contenedor contenedor-formulario">
            <div class="tarjeta-formulario">
                <span class="etiqueta">Iniciar sesión</span>
                <p>Accede con tu correo electrónico y tu contraseña.</p>

                <a href="{{ route('cliente.google.redirigir') }}" class="boton-google">
                    <img src="{{ asset('icons/google.svg') }}" alt="Google" class="icono-google-img">
                    <span>Continuar con Google</span>
                </a>

                <div class="separador-autenticacion">
                    <span>o ingresa con tu correo</span>
                </div>

                <form method="POST" action="{{ route('cliente.ingresar.validar') }}" class="rejilla-formulario">
                    @csrf

                    <div class="grupo-campo">
                        <label for="correo_electronico">Correo electrónico</label>
                        <input type="email" id="correo_electronico" name="correo_electronico"
                            value="{{ old('correo_electronico') }}" required>
                    </div>

                    <div class="grupo-campo">
                        <label for="contrasena">Contraseña</label>
                        <input type="password" id="contrasena" name="contrasena" required>
                    </div>

                    <div class="acciones-formulario">
                        <button type="submit" class="boton boton-primario">Ingresar</button>
                        <a href="{{ route('cliente.registro') }}" class="boton boton-secundario">Crear cuenta</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
