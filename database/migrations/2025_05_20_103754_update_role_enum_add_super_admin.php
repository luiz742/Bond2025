<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Para MySQL: usar SQL bruto para alterar ENUM
        DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin', 'admin', 'b2b', 'b2c') DEFAULT 'b2c'");
    }

    public function down(): void
    {
        // Reverter a alteração removendo o super_admin
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'b2b', 'b2c') DEFAULT 'b2c'");
    }
};
