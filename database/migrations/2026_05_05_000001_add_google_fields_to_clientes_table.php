<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('id_cliente');
            }

            if (!Schema::hasColumn('clientes', 'google_avatar')) {
                $table->string('google_avatar')->nullable()->after('correo_electronico');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (Schema::hasColumn('clientes', 'google_avatar')) {
                $table->dropColumn('google_avatar');
            }

            if (Schema::hasColumn('clientes', 'google_id')) {
                $table->dropUnique(['google_id']);
                $table->dropColumn('google_id');
            }
        });
    }
};