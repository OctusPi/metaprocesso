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
        Schema::create('comissions_members', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('organ')->constrained('organs');
            $table->foreignId('unit')->constrained('units');
            $table->foreignId('comission')->constrained('comissions');
            $table->string('name');
            $table->integer('responsibility');
            $table->string('number_doc')->nullable();
            $table->string('document')->nullable();
            $table->string('description')->nullable();
            $table->date('start_term');
            $table->date('end_term')->nullable();
            $table->boolean('status')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comissions_members');
    }
};
