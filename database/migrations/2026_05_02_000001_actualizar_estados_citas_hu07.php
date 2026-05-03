<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE citas MODIFY estado ENUM('registrada','confirmada','cancelada','completada','inasistencia') NOT NULL DEFAULT 'registrada'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE citas MODIFY estado ENUM('registrada','confirmada','cancelada','completada') NOT NULL DEFAULT 'registrada'");
    }
};
