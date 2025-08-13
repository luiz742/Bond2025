<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // Para quotes
    public function up()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('chain_id')->nullable()->index();
        });
    }

    public function down()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn('chain_id');
        });
    }
};
