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
            $table->dateTime('date_hour_ini');
            $table->integer('year_pca');
            $table->integer('type');
            $table->integer('modality');
            $table->foreignId('organ_id')->constrained('organs');
            $table->json('units');
            $table->json('ordinators');
            $table->foreignId('comission_id')->constrained('comissions');
            $table->json('comission_members');
            $table->string('comission_address');
            $table->foreignId('author_id')->constrained('users');
            $table->text('description');
            $table->integer('status');
            $table->string('initial_value')->nullable();
            $table->string('winner_value')->nullable();
            $table->json('dfds');
            $table->index('date_hour_ini');
            $table->integer('acquisition');
            $table->integer('acquisition_type');
            $table->integer('installment_type');
            $table->text('installment_justification')->nullable();
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
    