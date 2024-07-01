<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('price_records_proposals', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('protocol');
            $table->string('ip');
            $table->date('date_ini');
            $table->string('hour_ini')->nullable();
            $table->foreignId('organ')->constrained('organs');
            $table->foreignId('process')->constrained('processes');
            $table->foreignId('price_record')->constrained('price_records');
            $table->integer('modality');
            $table->json('supplier')->nullable();
            $table->json('items')->nullable();
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_records_proposals');
    }
};
