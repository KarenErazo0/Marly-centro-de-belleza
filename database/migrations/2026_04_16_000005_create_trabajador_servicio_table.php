<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajador_servicio', function (Blueprint $table) {
            $table->unsignedInteger('id_trabajador');
            $table->unsignedInteger('id_servicio');

            $table->primary(['id_trabajador', 'id_servicio']);
            $table->foreign('id_trabajador')->references('id_trabajador')->on('trabajadores')->onDelete('cascade');
            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajador_servicio');
    }
};
