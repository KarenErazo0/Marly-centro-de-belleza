<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('id_cliente');
            $table->string('nombre_completo', 100);
            $table->string('correo_electronico', 100)->unique();
            $table->string('telefono', 20);
            $table->string('contrasena', 255);
            $table->dateTime('fecha_registro');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
