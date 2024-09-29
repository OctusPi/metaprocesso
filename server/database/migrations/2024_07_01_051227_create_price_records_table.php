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
        Schema::create('price_records', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('protocol');
            $table->string('ip');
            $table->date('date_ini');
            $table->date('date_fin')->nullable();
            $table->foreignId('process_id')->constrained('processes');
            $table->foreignId('organ_id')->constrained('organs');
            $table->foreignId('comission_id')->constrained('comissions');
            $table->json('comission_members')->nullable();
            $table->json('suppliers')->nullable();
            $table->text('suppliers_justification')->nullable();
            $table->foreignId('author_id')->constrained('users');
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_records');
    }
};
