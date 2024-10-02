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
            $table->foreignId('organ_id')->constrained('organs');
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('demandant_id')->constrained('demandants');
            $table->foreignId('ordinator_id')->constrained('ordinators');
            $table->foreignId('comission_id')->constrained('comissions');
            $table->json('comission_members')->nullable();
            $table->date('date_ini');
            $table->string('year_pca', 5);
            $table->integer('acquisition_type'); // tipo de aquisições possiveis vide lei
            $table->integer('suggested_hiring'); // tipo de contrataçoes possiveis vide lei
            $table->text('description');
            $table->text('justification');
            $table->text('justification_quantity')->nullable();
            $table->string('estimated_value')->nullable();
            $table->date('estimated_date');
            $table->integer('priority');
            $table->boolean('bonds')->default(false); 
            $table->boolean('price_taking')->default(false);
            $table->integer('status');
            $table->foreignId('author_id')->constrained('users');
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
