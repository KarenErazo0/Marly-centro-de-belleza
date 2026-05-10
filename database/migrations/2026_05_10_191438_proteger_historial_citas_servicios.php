<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cita_servicio', function (Blueprint $table) {
            $table->dropForeign(['id_servicio']);
            $table->foreign('id_servicio')
                ->references('id_servicio')
                ->on('servicios')
                ->restrictOnDelete();
        });

        Schema::table('cita_detalle', function (Blueprint $table) {
            $table->dropForeign(['id_servicio']);
            $table->foreign('id_servicio')
                ->references('id_servicio')
                ->on('servicios')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cita_servicio', function (Blueprint $table) {
            $table->dropForeign(['id_servicio']);
            $table->foreign('id_servicio')
                ->references('id_servicio')
                ->on('servicios')
                ->cascadeOnDelete();
        });

        Schema::table('cita_detalle', function (Blueprint $table) {
            $table->dropForeign(['id_servicio']);
            $table->foreign('id_servicio')
                ->references('id_servicio')
                ->on('servicios')
                ->cascadeOnDelete();
        });
    }
};