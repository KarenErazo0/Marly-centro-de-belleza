<?php

use App\Http\Controllers\Cliente\Autenticacion\ControladorAutenticacionCliente;
use App\Http\Controllers\Cliente\Cuenta\ControladorCuentaCliente;
use App\Http\Controllers\Inicio\InicioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InicioController::class, 'index'])->name('inicio');

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
    });
});
