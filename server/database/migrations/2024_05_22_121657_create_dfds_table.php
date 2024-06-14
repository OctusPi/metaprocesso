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
        Schema::create('dfds', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('protocol')->unique();
            $table->string('ip')->nullable();
            $table->foreignId('organ')->constrained('organs');
            $table->foreignId('unit')->constrained('units');
            $table->foreignId('demandant')->constrained('demandants');
            $table->foreignId('ordinator')->constrained('ordinators');
            $table->foreignId('comission')->constrained('comissions');
            $table->json('comission_members')->nullable();
            $table->string('code', 50);
            $table->date('date_ini');
            $table->string('year_pca', 5);
            $table->integer('acquisition_type'); // tipo de aquisições possiveis vide lei
            $table->integer('suggested_hiring'); // tipo de contrataçoes possiveis vide lei
            $table->string('description');
            $table->string('justification');
            $table->text('justification_quantity')->nullable();
            $table->double('estimated_value');
            $table->date('estimated_date');
            $table->integer('priority');
            $table->boolean('bonds'); //vinculos outros dfds
            $table->integer('status');
            $table->foreignId('author')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dfds');
    }
};
