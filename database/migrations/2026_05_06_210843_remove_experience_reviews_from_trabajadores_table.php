<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trabajadores', function (Blueprint $table) {
            if (Schema::hasColumn('trabajadores', 'anios_experiencia')) {
                $table->dropColumn('anios_experiencia');
            }

            if (Schema::hasColumn('trabajadores', 'total_resenas')) {
                $table->dropColumn('total_resenas');
            }

            if (Schema::hasColumn('trabajadores', 'calificacion')) {
                $table->dropColumn('calificacion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trabajadores', function (Blueprint $table) {
            if (!Schema::hasColumn('trabajadores', 'anios_experiencia')) {
                $table->unsignedTinyInteger('anios_experiencia')->default(0)->after('especialidad');
            }

            if (!Schema::hasColumn('trabajadores', 'total_resenas')) {
                $table->unsignedInteger('total_resenas')->default(0)->after('anios_experiencia');
            }

            if (!Schema::hasColumn('trabajadores', 'calificacion')) {
                $table->decimal('calificacion', 2, 1)->default(5.0)->after('especialidad');
            }
        });
    }
};