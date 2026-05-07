@extends('plantillas.app')

@section('title', 'Restablecer contraseña | Marly Centro de Belleza')

@section('content')
<section class="seccion-formulario">
    <div class="contenedor contenedor-formulario">
        <div class="tarjeta-formulario">
            <span class="etiqueta">Nueva contraseña</span>
            <h1>Crea una nueva contraseña</h1>
            <p>Escribe una contraseña segura para recuperar el acceso a tu cuenta.</p>

            <form method="POST" action="{{ route('cliente.password.actualizar') }}" class="rejilla-formulario">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="correo_electronico" value="{{ $correo }}">

                <div class="grupo-campo">
                    <label for="contrasena">Nueva contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" required minlength="8">
                </div>

                <div class="grupo-campo">
                    <label for="contrasena_confirmation">Confirmar nueva contraseña</label>
                    <input type="password" id="contrasena_confirmation" name="contrasena_confirmation" required minlength="8">
                </div>

                <div class="acciones-formulario">
                    <button type="submit" class="boton boton-primario">Actualizar contraseña</button>
                    <a href="{{ route('cliente.ingresar') }}" class="boton boton-secundario">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection