<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->increments('id_cita');
            $table->unsignedInteger('id_cliente');
            $table->unsignedInteger('id_trabajador');
            $table->string('nombre_cliente', 100);
            $table->string('telefono_contacto', 20);
            $table->date('fecha_cita');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->unsignedInteger('duracion_total_minutos');
            $table->text('notas')->nullable();
            $table->enum('estado', ['registrada', 'confirmada', 'cancelada', 'completada', 'inasistencia'])->default('registrada');
            $table->dateTime('fecha_registro');

            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->onDelete('cascade');
            $table->foreign('id_trabajador')->references('id_trabajador')->on('trabajadores')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
