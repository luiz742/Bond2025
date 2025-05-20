<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('tax_registration_number')->nullable();
            $table->enum('role', ['admin', 'b2b', 'b2c'])->default('b2c');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'contact_number',
                'tax_registration_number',
                'role',
            ]);
        });
    }
};
