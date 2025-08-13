<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyInvoiceServicesForeignKeyOnDeleteCascade extends Migration
{
    public function up()
    {
        Schema::table('invoice_services', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']); // Remove FK atual

            $table->foreign('invoice_id')
                ->references('id')->on('invoices')
                ->onDelete('cascade'); // Cria FK com ON DELETE CASCADE
        });
    }

    public function down()
    {
        Schema::table('invoice_services', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']); // Remove FK com cascade

            $table->foreign('invoice_id')
                ->references('id')->on('invoices')
                ->onDelete('restrict'); // Volta para padrão restritivo (ou o que tinha antes)
        });
    }
}
