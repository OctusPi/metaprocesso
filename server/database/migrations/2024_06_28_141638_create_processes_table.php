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
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('protocol');
            $table->string('ip');
            $table->date('date_ini');
            $table->string('hour_ini')->nullable();
            $table->integer('year_pca');
            $table->integer('type');
            $table->integer('modality');
            $table->foreignId('organ')->constrained('organs');
            $table->json('units');
            $table->json('ordinators');
            $table->foreignId('comission')->constrained('comissions');
            $table->json('comission_members');
            $table->string('comission_address');
            $table->foreignId('author')->constrained('users');
            $table->text('description');
            $table->integer('situation');
            $table->string('initial_value')->nullable();
            $table->string('winner_value')->nullable();
            $table->json('dfds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proccesses');
    }
};
    