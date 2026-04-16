@extends('plantillas.app')

@section('title', 'Registro de cliente | Marly Centro de Belleza')

@section('content')
<section class="seccion-formulario">
    <div class="contenedor contenedor-formulario">
        <div class="tarjeta-formulario">
            <h1>Crear cuenta</h1>
            <p>Completa tus datos para registrarte en la plataforma.</p>

            <form method="POST" action="{{ route('cliente.registro.guardar') }}" class="rejilla-formulario">
                @csrf

                <div class="grupo-campo">
                    <label for="nombre_completo">Nombre completo</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" value="{{ old('nombre_completo') }}" required>
                </div>

                <div class="grupo-campo">
                    <label for="correo_electronico">Correo electrónico</label>
                    <input type="email" id="correo_electronico" name="correo_electronico" value="{{ old('correo_electronico') }}" required>
                </div>

                <div class="grupo-campo">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" required>
                </div>

                <div class="grupo-campo">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" required>
                </div>

                <div class="grupo-campo">
                    <label for="contrasena_confirmation">Confirmar contraseña</label>
                    <input type="password" id="contrasena_confirmation" name="contrasena_confirmation" required>
                </div>

                <div class="acciones-formulario">
                    <button type="submit" class="boton boton-primario">Registrarme</button>
                    <a href="{{ route('cliente.ingresar') }}" class="boton boton-secundario">Ya tengo cuenta</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
