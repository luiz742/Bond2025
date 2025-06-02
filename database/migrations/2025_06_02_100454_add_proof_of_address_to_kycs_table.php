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
        Schema::table('kycs', function (Blueprint $table) {
            $table->string('proof_of_address')->nullable();
        });
    }

    public function down()
    {
        Schema::table('kycs', function (Blueprint $table) {
            $table->dropColumn('proof_of_address');
        });
    }

};
