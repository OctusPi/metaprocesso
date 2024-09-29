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
            $table->foreignId('process_id')->constrained('processes');
            $table->foreignId('organ_id')->constrained('organs');
            $table->foreignId('comission_id')->constrained('comissions');
            $table->foreignId('etp_id')->constrained('etps');
            $table->foreignId('author_id')->constrained('users');
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
            $table->date('emission');
            $table->integer('type');
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
