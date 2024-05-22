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
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('organ_id')->constrained('organs');
            $table->foreignId('catalog_id')->constrained('catalogs');
            $table->string('code', 50);
            $table->string('name');
            $table->text('description');
            $table->string('und', 50);
            $table->string('volume', 50)->nullable();
            $table->integer('origin');
            $table->integer('type');
            $table->integer('category');
            $table->foreignId('subcategory_id')->constrained('catalog_items_subcategories');
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_items');
    }
};
