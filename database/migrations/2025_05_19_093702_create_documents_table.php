<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // Foreign key para o usuário dono do client
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Foreign key para o serviço ao qual o client pertence
            $table->foreignId('service_id')->constrained()->onDelete('cascade');

            $table->string('name'); // Nome do client (exemplo)

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
