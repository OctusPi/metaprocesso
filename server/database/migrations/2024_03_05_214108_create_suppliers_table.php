<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('cnpj', 20);
            $table->string('agent')->nullable();
            $table->string('cpf', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 220)->nullable();
            $table->string('address');
            $table->json('services')->nullable();
            $table->integer('modality');
            $table->integer('size')->nullable();
            $table->foreignId('organ_id')->constrained('organs');
            $table->integer('flag')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
