<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->increments('id_servicio');
            $table->string('nombre_servicio', 100);
            $table->text('descripcion');
            $table->decimal('precio', 10, 2);
            $table->integer('duracion_minutos');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
