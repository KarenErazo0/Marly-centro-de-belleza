<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cita_servicio', function (Blueprint $table) {
            $table->unsignedInteger('id_cita');
            $table->unsignedInteger('id_servicio');

            $table->primary(['id_cita', 'id_servicio']);
            $table->foreign('id_cita')->references('id_cita')->on('citas')->onDelete('cascade');
            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cita_servicio');
    }
};
