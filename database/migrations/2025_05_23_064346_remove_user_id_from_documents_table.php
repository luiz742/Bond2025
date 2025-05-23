<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Caso exista uma foreign key
            $table->dropColumn('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(); // ou não nullable, conforme era antes
        });
    }
};
