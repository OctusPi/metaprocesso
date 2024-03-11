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
        Schema::create('stockentryitems', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('stockentry_id')->constrained('stockentries');
            $table->foreignId('item_id')->constrained('items');
            $table->integer('quantity');
            $table->double('current_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockentrieitems');
    }
};
