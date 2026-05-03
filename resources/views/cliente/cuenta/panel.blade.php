@extends('plantillas.app')

@section('title', 'Mi cuenta | Marly Centro de Belleza')

@section('content')
<section class="seccion-cuenta">
    <div class="contenedor rejilla-cuenta">
        <div class="tarjeta-cuenta bienvenida-cuenta cuenta-principal">
            <div>
                <h1>Hola, {{ $cliente->nombre_completo }}</h1>
                <p>Desde aquí puedes gestionar tus citas, editar tus datos personales o cerrar sesión.</p>

                <div class="acciones-panel-cliente">
                    <a href="{{ route('cliente.citas.index') }}" class="boton boton-primario">Ver mis citas</a>
                    <a href="{{ route('cliente.citas.agendar.servicios') }}" class="boton boton-secundario">Agendar nueva cita</a>
                    <button type="button" class="boton boton-secundario" id="btn-editar-cuenta">Editar datos personales</button>
                </div>
            </div>

            <form action="{{ route('cliente.salir') }}" method="POST" class="form-cerrar-sesion-cuenta">
                @csrf
                <button type="submit" class="boton boton-secundario">Cerrar sesión</button>
            </form>
        </div>

        <div class="tarjeta-cuenta panel-edicion-cuenta" id="panel-edicion-cuenta">
            <h2>Modificar datos de la cuenta</h2>
            <form method="POST" action="{{ route('cliente.cuenta.actualizar') }}" class="rejilla-formulario">
                @csrf
                @method('PUT')

                <div class="grupo-campo">
                    <label for="nombre_completo">Nombre completo</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" value="{{ old('nombre_completo', $cliente->nombre_completo) }}" required>
                </div>

                <div class="grupo-campo">
                    <label for="correo_electronico">Correo electrónico</label>
                    <input type="email" id="correo_electronico" name="correo_electronico" value="{{ old('correo_electronico', $cliente->correo_electronico) }}" required>
                </div>

                <div class="grupo-campo">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $cliente->telefono) }}" required>
                </div>

                <div class="grupo-campo">
                    <label for="contrasena">Nueva contraseña (opcional)</label>
                    <input type="password" id="contrasena" name="contrasena" minlength="8">
                    <small class="ayuda-campo">Si deseas cambiarla, usa mínimo 8 caracteres.</small>
                </div>

                <div class="grupo-campo">
                    <label for="contrasena_confirmation">Confirmar nueva contraseña</label>
                    <input type="password" id="contrasena_confirmation" name="contrasena_confirmation" minlength="8">
                </div>

                <div class="acciones-formulario">
                    <button type="submit" class="boton boton-primario">Guardar cambios</button>
                </div>
            </form>
        </div>

        <div class="tarjeta-cuenta tarjeta-eliminar">
            <h2>Eliminar cuenta</h2>
            <p>Esta acción eliminará tu cuenta de cliente del sistema. Si continúas, deberás registrarte nuevamente para volver a ingresar.</p>

            <form method="POST" action="{{ route('cliente.cuenta.eliminar') }}" class="rejilla-formulario">
                @csrf
                @method('DELETE')

                <label class="campo-confirmacion">
                    <input type="checkbox" name="confirmar_eliminacion" value="1">
                    Confirmo que deseo eliminar mi cuenta.
                </label>

                <div class="acciones-formulario">
                    <button type="submit" class="boton boton-peligro">Eliminar cuenta</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
