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
        Schema::table('etps', function (Blueprint $table) {
            $table->text('object_description')->nullable()->change();
            $table->text('object_classification')->nullable()->change();
            $table->text('necessity')->nullable()->change();
            $table->text('contract_forecast')->nullable()->change();
            $table->text('contract_requirements')->nullable()->change();
            $table->text('market_survey')->nullable()->change();
            $table->text('contract_calculus_memories')->nullable()->change();
            $table->text('contract_expected_price')->nullable()->change();
            $table->text('solution_full_description')->nullable()->change();
            $table->text('solution_parcel_justification')->nullable()->change();
            $table->text('correlated_contracts')->nullable()->change();
            $table->text('contract_alignment')->nullable()->change();
            $table->text('expected_results')->nullable()->change();
            $table->text('contract_previous_actions')->nullable()->change();
            $table->text('ambiental_impacts')->nullable()->change();
            $table->integer('viability_declaration')->nullable()->change();
            $table->integer('status')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etps', function (Blueprint $table) {
            $table->text('object_description')->change();
            $table->text('object_classification')->change();
            $table->text('necessity')->change();
            $table->text('contract_forecast')->change();
            $table->text('contract_requirements')->change();
            $table->text('market_survey')->change();
            $table->text('contract_calculus_memories')->change();
            $table->text('contract_expected_price')->change();
            $table->text('solution_full_description')->change();
            $table->text('solution_parcel_justification')->change();
            $table->text('correlated_contracts')->change();
            $table->text('contract_alignment')->change();
            $table->text('expected_results')->change();
            $table->text('contract_previous_actions')->change();
            $table->text('ambiental_impacts')->change();
            $table->integer('viability_declaration')->change();
        });
    }
};
