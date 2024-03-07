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
        Schema::create('dfds', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('cod', 50);
            $table->date('date_ini');
            $table->integer('category');
            $table->text('obj');
            $table->text('description')->nullable();
            $table->foreignId('organ')->constrained('organs');
            $table->foreignId('unit')->constrained('units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dfds');
    }
};
