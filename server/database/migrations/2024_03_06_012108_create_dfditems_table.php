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
        Schema::create('dfditems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dfd')->constrained('dfds');
            $table->foreignId('item')->constrained('items');
            $table->integer('quantity');
            $table->foreignId('program')->constrained('programs');
            $table->foreignId('dotation')->constrained('dotations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dfditems');
    }
};
