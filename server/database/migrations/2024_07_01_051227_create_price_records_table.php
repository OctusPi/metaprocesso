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
        Schema::create('price_records', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('protocol');
            $table->string('ip');
            $table->date('date_ini');
            $table->date('date_fin')->nullable();
            $table->foreignId('process')->constrained('processes');
            $table->foreignId('organ')->constrained('organs');
            $table->json('units')->nullable();
            $table->foreignId('comission')->constrained('comissions');
            $table->json('comission_members')->nullable();
            $table->json('suppliers')->nullable();
            $table->foreignId('author')->constrained('users');
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_records');
    }
};
