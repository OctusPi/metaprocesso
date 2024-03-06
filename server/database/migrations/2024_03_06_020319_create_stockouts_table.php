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
        Schema::create('stockouts', function (Blueprint $table) {
            $table->id();
            $table->date('date_ini');
            $table->string('cod', 50);
            $table->foreignId('organ')->constrained('organs');
            $table->foreignId('unit')->constrained('units');
            $table->foreignId('sector')->constrained('sectors');
            $table->text('description')->nullable();
            $table->string('issuer')->nullable();
            $table->string('requester')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockouts');
    }
};
