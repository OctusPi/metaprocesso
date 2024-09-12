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
        Schema::create('refterms', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('process')->constrained('processes');
            $table->foreignId('organ')->constrained('organs');
            $table->foreignId('comission')->constrained('comissions');
            $table->foreignId('etp')->constrained('etps');
            $table->string('protocol')->unique();
            $table->text('necessity');
            $table->text('contract_forecast');
            $table->text('contract_requirements');
            $table->text('contract_expected_price');
            $table->text('market_survey');
            $table->text('solution_full_description');
            $table->text('ambiental_impacts');
            $table->text('correlated_contracts');
            $table->text('object_execution_model');
            $table->text('contract_management_model');
            $table->text('payment_measure_criteria');
            $table->text('supplier_selection_criteria');
            $table->text('funds_suitability');
            $table->text('parts_obligation');
            $table->integer('status');
            $table->date('emission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refterms');
    }
};
