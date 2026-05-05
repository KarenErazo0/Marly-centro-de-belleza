<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_sitio', function (Blueprint $table) {
            $table->id();
            $table->string('hero_imagen')->nullable();
            $table->string('contacto_ubicacion')->default('Pasto, Nariño');
            $table->string('contacto_telefono', 30)->default('7291317');
            $table->string('contacto_correo')->default('marly@centrobelleza.com');
            $table->text('contacto_horario')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('whatsapp_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_sitio');
    }
};
