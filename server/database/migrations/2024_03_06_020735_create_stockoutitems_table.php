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
        Schema::create('stockoutitems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stockout')->constrained('stockouts');
            $table->foreignId('item')->constrained('items');
            $table->integer('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockoutitems');
    }
};
