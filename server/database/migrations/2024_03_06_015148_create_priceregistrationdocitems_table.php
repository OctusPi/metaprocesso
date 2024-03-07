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
        Schema::create('priceregistrationdocitems', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('priceregistrationdoc')->constrained('priceregistrationdocs');
            $table->foreignId('item')->constrained('items');
            $table->integer('quantity');
            $table->double('unitary_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('priceregistrationdocitems');
    }
};
