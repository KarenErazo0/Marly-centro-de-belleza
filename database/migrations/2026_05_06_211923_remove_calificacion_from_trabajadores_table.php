<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trabajadores', function (Blueprint $table) {
            if (Schema::hasColumn('trabajadores', 'calificacion')) {
                $table->dropColumn('calificacion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trabajadores', function (Blueprint $table) {
            if (!Schema::hasColumn('trabajadores', 'calificacion')) {
                $table->decimal('calificacion', 2, 1)->default(5.0)->after('especialidad');
            }
        });
    }
};