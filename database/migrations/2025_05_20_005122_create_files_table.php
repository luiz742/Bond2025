<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->string('path')->nullable(); // null enquanto não enviado
            $table->enum('status', ['pending', 'approved', 'rejected'])->nullable(); // null = "não enviado"
            $table->timestamps();

            $table->unique(['client_id', 'document_id']); // evita duplicidade
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
