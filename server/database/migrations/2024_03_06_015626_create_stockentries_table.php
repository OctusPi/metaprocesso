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
        Schema::create('stockentries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('date_ini');
            $table->string('invoice', 50);
            $table->text('danfe');
            $table->foreignId('purchaseorder_id')->constrained('purchaseorders');
            $table->foreignId('contract_id')->constrained('contracts');
            $table->integer('quantity');
            $table->double('current_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockentries');
    }
};
