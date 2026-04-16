<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cita_detalle', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_cita');
            $table->unsignedInteger('id_servicio');
            $table->unsignedInteger('id_trabajador');
            $table->string('area', 50)->nullable();

            $table->foreign('id_cita')->references('id_cita')->on('citas')->onDelete('cascade');
            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->onDelete('cascade');
            $table->foreign('id_trabajador')->references('id_trabajador')->on('trabajadores')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cita_detalle');
    }
};
