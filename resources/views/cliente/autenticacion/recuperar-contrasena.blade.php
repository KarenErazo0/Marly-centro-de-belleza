@extends('plantillas.app')

@section('title', 'Recuperar contraseña | Marly Centro de Belleza')

@section('content')
<section class="seccion-formulario">
    <div class="contenedor contenedor-formulario">
        <div class="tarjeta-formulario">
            <span class="etiqueta">Recuperar contraseña</span>
            <h1>¿Olvidaste tu contraseña?</h1>
            <p>Ingresa el correo electrónico registrado en tu cuenta y te enviaremos un enlace para crear una nueva contraseña.</p>

            <form method="POST" action="{{ route('cliente.password.enviar') }}" class="rejilla-formulario">
                @csrf

                <div class="grupo-campo">
                    <label for="correo_electronico">Correo electrónico</label>
                    <input type="email" id="correo_electronico" name="correo_electronico"
                        value="{{ old('correo_electronico') }}" required>
                </div>

                <div class="acciones-formulario">
                    <button type="submit" class="boton boton-primario">Enviar enlace</button>
                    <a href="{{ route('cliente.ingresar') }}" class="boton boton-secundario">Volver al inicio de sesión</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection