<?php

use App\Http\Controllers\Admin\ControladorAdminDashboard;
use App\Http\Controllers\Cliente\Autenticacion\ControladorAutenticacionCliente;
use App\Http\Controllers\Cliente\Citas\ControladorCitasCliente;
use App\Http\Controllers\Cliente\Cuenta\ControladorCuentaCliente;
use App\Http\Controllers\Inicio\InicioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InicioController::class, 'index'])->name('inicio');


Route::prefix('admin')->name('admin.')->middleware('admin.sesion')->group(function () {
    Route::get('/dashboard', [ControladorAdminDashboard::class, 'index'])->name('dashboard');
});

Route::prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/registro', [ControladorAutenticacionCliente::class, 'mostrarRegistro'])->name('registro');
    Route::post('/registro', [ControladorAutenticacionCliente::class, 'registrar'])->name('registro.guardar');
    Route::get('/ingresar', [ControladorAutenticacionCliente::class, 'mostrarIngreso'])->name('ingresar');
    Route::post('/ingresar', [ControladorAutenticacionCliente::class, 'ingresar'])->name('ingresar.validar');
    Route::post('/salir', [ControladorAutenticacionCliente::class, 'salir'])->name('salir');

    Route::middleware('cliente.sesion')->group(function () {
        Route::get('/cuenta', [ControladorCuentaCliente::class, 'index'])->name('cuenta');
        Route::put('/cuenta', [ControladorCuentaCliente::class, 'update'])->name('cuenta.actualizar');
        Route::delete('/cuenta', [ControladorCuentaCliente::class, 'destroy'])->name('cuenta.eliminar');

        Route::prefix('citas')->name('citas.')->group(function () {
            Route::get('/', [ControladorCitasCliente::class, 'index'])->name('index');
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
