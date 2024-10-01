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
        Schema::create('risk_maps', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('author_id')->constrained('users');
            $table->foreignId('process_id')->constrained('processes');
            $table->foreignId('comission_id')->constrained('comissions');
            $table->foreignId('organ_id')->constrained('organs');
            $table->date('date_version');
            $table->string('version', 10);
            $table->integer('phase');
            $table->text('description');
            $table->json('comission_members');
            $table->json('riskiness');
            $table->json('accompaniments');
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_maps');
    }
};
