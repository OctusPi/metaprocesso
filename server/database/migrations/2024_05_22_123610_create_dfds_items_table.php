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
        Schema::create('dfds_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('dfd_id')->constrained('dfds');
            $table->foreignId('item_id')->constrained('catalog_items');
            $table->integer('quantity');
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('dotation_id')->nullable()->constrained('dotations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dfds_items');
    }
};
