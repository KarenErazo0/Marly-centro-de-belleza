<?php

use App\Http\Controllers\Admin\ControladorAdminDashboard;
use App\Http\Controllers\Cliente\Autenticacion\ControladorAutenticacionCliente;
use App\Http\Controllers\Cliente\Autenticacion\ControladorRecuperacionContrasena;
use App\Http\Controllers\Cliente\Citas\ControladorCitasCliente;
use App\Http\Controllers\Cliente\Cuenta\ControladorCuentaCliente;
use App\Http\Controllers\Inicio\InicioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InicioController::class, 'index'])->name('inicio');

Route::prefix('admin')->name('admin.')->middleware('admin.sesion')->group(function () {
    Route::get('/dashboard', [ControladorAdminDashboard::class, 'index'])->name('dashboard');
    Route::patch('/configuracion', [ControladorAdminDashboard::class, 'actualizarConfiguracion'])->name('configuracion.actualizar');

    Route::post('/servicios', [ControladorAdminDashboard::class, 'guardarServicio'])->name('servicios.guardar');
    Route::put('/servicios/{servicio}', [ControladorAdminDashboard::class, 'actualizarServicio'])->name('servicios.actualizar');
    Route::patch('/servicios/{servicio}/estado', [ControladorAdminDashboard::class, 'cambiarEstadoServicio'])->name('servicios.estado');
    Route::delete('/servicios/{servicio}', [ControladorAdminDashboard::class, 'eliminarServicio'])->name('servicios.eliminar');

    Route::post('/personal/secciones', [ControladorAdminDashboard::class, 'crearSeccionPersonal'])->name('personal.secciones.guardar');
    Route::put('/personal/secciones/{servicio}', [ControladorAdminDashboard::class, 'actualizarSeccionPersonal'])->name('personal.secciones.actualizar');
    Route::delete('/personal/secciones/{servicio}', [ControladorAdminDashboard::class, 'eliminarSeccionPersonal'])->name('personal.secciones.eliminar');

    Route::post('/personal/trabajadores', [ControladorAdminDashboard::class, 'guardarTrabajador'])->name('personal.trabajadores.guardar');
   Route::put('/personal/secciones/{servicio}/trabajadores/{trabajador}', [ControladorAdminDashboard::class, 'actualizarTrabajador'])
    ->name('personal.trabajadores.actualizar');
    Route::post('/personal/trabajadores/{trabajador}/duplicar', [ControladorAdminDashboard::class, 'duplicarTrabajadorEnServicio'])
    ->name('personal.trabajadores.duplicar');
    Route::patch('/personal/trabajadores/{trabajador}/estado', [ControladorAdminDashboard::class, 'cambiarEstadoTrabajador'])->name('personal.trabajadores.estado');
    Route::delete('/personal/secciones/{servicio}/trabajadores/{trabajador}', [ControladorAdminDashboard::class, 'eliminarTrabajadorDeServicio'])
    ->name('personal.trabajadores.eliminar-servicio');
    Route::delete('/personal/trabajadores/{trabajador}', [ControladorAdminDashboard::class, 'eliminarTrabajador'])->name('personal.trabajadores.eliminar');

    Route::patch('/citas/{cita}/asistencia', [ControladorAdminDashboard::class, 'actualizarAsistencia'])->name('citas.asistencia');
});

Route::prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/registro', [ControladorAutenticacionCliente::class, 'mostrarRegistro'])->name('registro');
    Route::post('/registro', [ControladorAutenticacionCliente::class, 'registrar'])->name('registro.guardar');

    Route::get('/ingresar', [ControladorAutenticacionCliente::class, 'mostrarIngreso'])->name('ingresar');
    Route::post('/ingresar', [ControladorAutenticacionCliente::class, 'ingresar'])->name('ingresar.validar');

    Route::get('/recuperar-contrasena', [ControladorRecuperacionContrasena::class, 'mostrarSolicitud'])->name('password.solicitar');
    Route::post('/recuperar-contrasena', [ControladorRecuperacionContrasena::class, 'enviarEnlace'])->name('password.enviar');
    Route::get('/restablecer-contrasena/{token}', [ControladorRecuperacionContrasena::class, 'mostrarRestablecer'])->name('password.reset');
    Route::post('/restablecer-contrasena', [ControladorRecuperacionContrasena::class, 'actualizarContrasena'])->name('password.actualizar');

    Route::get('/google', [ControladorAutenticacionCliente::class, 'redirigirAGoogle'])->name('google.redirigir');
    Route::get('/google/callback', [ControladorAutenticacionCliente::class, 'procesarGoogleCallback'])->name('google.callback');

    Route::post('/salir', [ControladorAutenticacionCliente::class, 'salir'])->name('salir');

    Route::middleware('cliente.sesion')->group(function () {
        Route::get('/cuenta', [ControladorCuentaCliente::class, 'index'])->name('cuenta');
        Route::put('/cuenta', [ControladorCuentaCliente::class, 'update'])->name('cuenta.actualizar');
        Route::delete('/cuenta', [ControladorCuentaCliente::class, 'destroy'])->name('cuenta.eliminar');

        Route::prefix('citas')->name('citas.')->group(function () {
            Route::get('/', [ControladorCitasCliente::class, 'index'])->name('index');
            Route::get('/{cita}/editar', [ControladorCitasCliente::class, 'editar'])->name('editar');
            Route::put('/{cita}', [ControladorCitasCliente::class, 'actualizar'])->name('actualizar');
            Route::delete('/{cita}', [ControladorCitasCliente::class, 'cancelar'])->name('cancelar');

            Route::get('/agendar/servicios', [ControladorCitasCliente::class, 'seleccionarServicios'])->name('agendar.servicios');
            Route::post('/agendar/servicios', [ControladorCitasCliente::class, 'guardarServicios'])->name('agendar.servicios.guardar');

            Route::get('/agendar/trabajador', [ControladorCitasCliente::class, 'seleccionarTrabajador'])->name('agendar.trabajador');
            Route::post('/agendar/trabajador', [ControladorCitasCliente::class, 'guardarTrabajador'])->name('agendar.trabajador.guardar');

            Route::get('/agendar/horario', [ControladorCitasCliente::class, 'seleccionarHorario'])->name('agendar.horario');
            Route::post('/agendar/horario', [ControladorCitasCliente::class, 'guardarHorario'])->name('agendar.horario.guardar');

            Route::get('/agendar/confirmacion', [ControladorCitasCliente::class, 'confirmar'])->name('agendar.confirmacion');
            Route::post('/agendar/confirmacion', [ControladorCitasCliente::class, 'registrar'])->name('agendar.registrar');
        });
    });
});