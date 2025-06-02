<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeKycFieldsNullable extends Migration
{
    public function up()
    {
        Schema::table('kycs', function (Blueprint $table) {
            $table->string('company_trade_license')->nullable()->change();
            $table->string('tax_certificate')->nullable()->change();
            $table->string('passport_copy')->nullable()->change();
            $table->string('id_proof')->nullable()->change();
            $table->string('proof_of_address')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('kycs', function (Blueprint $table) {
            $table->string('company_trade_license')->nullable(false)->change();
            $table->string('tax_certificate')->nullable(false)->change();
            $table->string('passport_copy')->nullable(false)->change();
            $table->string('id_proof')->nullable(false)->change();
            $table->string('proof_of_address')->nullable(false)->change();
        });
    }
}
