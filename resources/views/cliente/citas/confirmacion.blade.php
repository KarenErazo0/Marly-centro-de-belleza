@extends('plantillas.app')

@section('title', 'Confirmar cita | Marly Centro de Belleza')

@section('content')
<section class="seccion-reservas">
    <div class="contenedor contenedor-reserva contenedor-confirmacion">
        <div class="pasos-reserva">
            <span class="paso-reserva completado">1. Servicios</span>
            <span class="paso-reserva completado">2. Profesional</span>
            <span class="paso-reserva completado">3. Fecha y hora</span>
            <span class="paso-reserva activo">4. Confirmación</span>
        </div>

        <div class="encabezado-seccion reserva-centro">
            <span class="etiqueta">Resumen de reserva</span>
            <p>Revisa el resumen y confirma tus datos antes de finalizar la reserva.</p>
        </div>

        <form action="{{ route('cliente.citas.agendar.registrar') }}" method="POST" class="tarjeta-confirmacion-reserva tarjeta-confirmacion-doble" id="form-confirmar-reserva">
            @csrf
            <aside class="resumen-confirmacion-pequeno">
                <h3>Resumen de la cita</h3>
                <p><strong>Servicio:</strong> {{ $servicios->pluck('nombre_servicio')->join(', ') }}</p>
                <p><strong>Profesional:</strong> {{ $trabajadores->pluck('nombre_completo')->join(', ') }}</p>
                <p><strong>Fecha:</strong> {{ ucfirst(\Carbon\Carbon::parse($reserva['fecha'])->translatedFormat('l, j \d\e F \d\e Y')) }}</p>
                <p><strong>Hora:</strong> {{ \Carbon\Carbon::createFromFormat('H:i', $reserva['hora'])->translatedFormat('h:i A') }}</p>
                <p><strong>Duración estimada:</strong> {{ $duracionTotal }} minutos</p>
                <p><strong>Precio estimado:</strong> ${{ number_format($servicios->sum('precio'), 0, ',', '.') }}</p>
                <div class="aviso-precio">
                    El precio es estimado y puede variar según el diseño, materiales o detalles finales del servicio.
                </div>
            </aside>

            <div class="datos-confirmacion-cliente">
                <div class="grupo-campo">
                    <label for="nombre_cliente">Nombre completo *</label>
                    <input type="text" id="nombre_cliente" name="nombre_cliente" value="{{ old('nombre_cliente', $cliente->nombre_completo) }}" required>
                </div>

                <div class="grupo-campo">
                    <label for="telefono_contacto">Teléfono *</label>
                    <input type="text" id="telefono_contacto" name="telefono_contacto" value="{{ old('telefono_contacto', $cliente->telefono) }}" required>
                </div>

                <div class="grupo-campo">
                    <label for="notas">Notas adicionales (opcional)</label>
                    <textarea id="notas" name="notas" rows="4" placeholder="¿Alguna preferencia o solicitud especial?">{{ old('notas') }}</textarea>
                </div>

                <div class="acciones-reserva-final">
                    <a href="{{ route('cliente.citas.agendar.horario') }}" class="boton boton-secundario">Volver</a>
                    <button type="submit" class="boton boton-primario">Confirmar cita</button>
                </div>
            </div>
        </form>
    </div>

    <div class="modal-confirmacion-cita" id="modal-confirmacion-cita" aria-hidden="true">
        <div class="modal-confirmacion-backdrop" data-modal-cancelar></div>
        <div class="modal-confirmacion-card" role="dialog" aria-modal="true" aria-labelledby="titulo-modal-confirmacion">
            <span class="etiqueta">Confirmación</span>
            <p id="titulo-modal-confirmacion">¿Estás seguro de agendar esta cita?</p>
            <small>Recuerda llegar a tiempo. El precio es estimado y puede variar dependiendo del diseño o detalles finales del servicio.</small>
            <div class="acciones-modal-confirmacion">
                <button type="button" class="boton boton-secundario" data-modal-cancelar>Revisar de nuevo</button>
                <button type="button" class="boton boton-primario" id="btn-modal-confirmar-cita">Sí, confirmar cita</button>
            </div>
        </div>
    </div>
</section>
@endsection
