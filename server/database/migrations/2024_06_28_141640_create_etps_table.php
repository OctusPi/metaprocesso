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
        Schema::create('etps', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('process_id')->constrained('processes');
            $table->foreignId('organ_id')->constrained('organs');
            $table->foreignId('comission_id')->constrained('comissions');
            $table->foreignId('author_id')->constrained('users');
            $table->string('protocol')->unique();
            $table->string('ip')->nullable();
            $table->date('emission');
            $table->integer('status')->default(0);
            $table->text('object_description')->nullable();
            $table->text('object_classification')->nullable();
            $table->text('necessity')->nullable();
            $table->text('contract_forecast')->nullable();
            $table->text('contract_requirements')->nullable();
            $table->text('market_survey')->nullable();
            $table->text('contract_calculus_memories')->nullable();
            $table->text('contract_expected_price')->nullable();
            $table->text('solution_full_description')->nullable();
            $table->text('solution_parcel_justification')->nullable();
            $table->text('correlated_contracts')->nullable();
            $table->text('contract_alignment')->nullable();
            $table->text('expected_results')->nullable();
            $table->text('contract_previous_actions')->nullable();
            $table->text('ambiental_impacts')->nullable();
            $table->integer('viability_declaration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etps');
    }
};
