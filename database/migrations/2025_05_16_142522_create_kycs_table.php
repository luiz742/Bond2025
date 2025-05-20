<?php

// database/migrations/xxxx_xx_xx_create_kycs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKycsTable extends Migration
{
    public function up()
    {
        Schema::create('kycs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['b2b', 'b2c']);

            // Campos B2B
            $table->string('company_trade_license')->nullable();
            $table->string('tax_certificate')->nullable();

            // Campos B2C
            $table->string('passport_copy')->nullable();
            $table->string('id_proof')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kycs');
    }
}
