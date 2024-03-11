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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('cod', 50);
            $table->integer('category');
            $table->text('obj');
            $table->text('description')->nullable();
            $table->foreignId('organ_id')->constrained('organs');
            $table->foreignId('unit_id')->constrained('units');
            $table->date('date_ini');
            $table->date('date_fin');
            $table->double('estimated_value');
            $table->double('approved_value');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->boolean('additive');
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
