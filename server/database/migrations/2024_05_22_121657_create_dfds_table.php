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
            $table->foreignId('organ_id')->constrained('organs');
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('demandant_id')->constrained('demandants');
            $table->foreignId('ordinator_id')->constrained('ordinators');
            $table->foreignId('comission_id')->constrained('comissions');
            $table->string('code', 50);
            $table->date('date_ini');
            $table->string('year_pca', 5);
            $table->integer('acquisition_type'); // tipo de aquisições possiveis vide lei
            $table->integer('suggested_hiring'); // tipo de contrataçoes possiveis vide lei
            $table->string('description');
            $table->string('justification');
            $table->double('estimated_value');
            $table->date('estimated_date');
            $table->integer('priority');
            $table->boolean('bonds'); //vinculos outros dfds

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
