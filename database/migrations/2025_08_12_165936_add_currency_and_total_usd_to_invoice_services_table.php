<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('invoice_services', function (Blueprint $table) {
            $table->string('currency', 10)->default('USD'); // Moeda original
            $table->decimal('total_usd', 15, 2)->nullable(); // Valor convertido
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_services', function (Blueprint $table) {
            //
        });
    }
};
