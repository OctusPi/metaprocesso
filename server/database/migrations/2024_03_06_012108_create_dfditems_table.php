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
        Schema::create('dfditems', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('dfd_id')->constrained('dfds');
            $table->foreignId('item_id')->constrained('items');
            $table->integer('quantity');
            $table->foreignId('program_id')->constrained('programs');
            $table->foreignId('dotation_id')->constrained('dotations');
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
