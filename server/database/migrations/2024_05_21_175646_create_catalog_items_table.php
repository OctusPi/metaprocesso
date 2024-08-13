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
            $table->foreignId('organ')->constrained('organs');
            $table->foreignId('catalog')->constrained('catalogs');
            $table->string('code', 50);
            $table->string('name');
            $table->text('description');
            $table->string('und', 50);
            $table->string('volume', 50)->nullable();
            $table->integer('origin');
            $table->integer('type');
            $table->integer('category');
            $table->foreignId('subcategory')->nullable()->constrained('catalog_items_subcategories')->nullOnDelete();
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
