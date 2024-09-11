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
            $table->string('token')->unique();
            $table->date('date_ini');
            $table->time('hour_ini');
            $table->date('date_fin')->nullable();
            $table->time('hour_fin')->nullable();
            $table->foreignId('organ')->constrained('organs');
            $table->foreignId('process')->constrained('processes');
            $table->foreignId('price_record')->constrained('price_records');
            $table->foreignId('author')->constrained('users');
            $table->foreignId('supplier')->nullable()->constrained('suppliers');
            $table->integer('modality');
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
