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
            $table->json('dfds');
            $table->string('number', 20);
            $table->text('general_info');
            $table->text('object_description');
            $table->string('object_classification');
            $table->text('necessity');
            $table->text('contract_forecast');
            $table->text('contract_requirements');
            $table->text('market_survey');
            $table->text('contract_calculus_memories');
            $table->string('contract_calculus_memories_file')->nullable();
            $table->string('contract_expected_price');
            $table->string('contract_expected_price_file')->nullable();
            $table->text('solution_full_description');
            $table->text('solution_parcel_justification');
            $table->text('correlated_contracts');
            $table->text('contract_alignment');
            $table->text('expected_results');
            $table->text('contract_previous_actions');
            $table->text('ambiental_impacts');
            $table->text('viability_declaration');
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
