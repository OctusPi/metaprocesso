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
        Schema::create('purchaseorders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('cod', 50);
            $table->date('date_ini');
            $table->foreignId('organ_id')->constrained('organs');
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('contract_id')->constrained('contracts');
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchaseorders');
    }
};
