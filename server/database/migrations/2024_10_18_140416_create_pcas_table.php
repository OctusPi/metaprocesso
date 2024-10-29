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
        Schema::create('pcas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('protocol')->unique();
            $table->foreignId('organ_id')->constrained('organs');
            $table->foreignId('comission_id')->constrained('comissions');
            $table->foreignId('author_id')->constrained('users');
            $table->string('reference_year');
            $table->date('emission');
            $table->string('price', 200);
            $table->json('comission_members');
            $table->text('observations')->nullable();
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pcas');
    }
};
