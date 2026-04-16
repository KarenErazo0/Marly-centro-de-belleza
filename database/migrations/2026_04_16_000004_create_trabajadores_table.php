<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->increments('id_trabajador');
            $table->string('nombre_completo', 100);
            $table->string('especialidad', 100);
            $table->unsignedTinyInteger('anios_experiencia');
            $table->unsignedInteger('total_resenas')->default(0);
            $table->decimal('calificacion', 2, 1)->default(5.0);
            $table->string('foto')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
