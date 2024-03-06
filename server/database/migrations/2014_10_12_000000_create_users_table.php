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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email', 220);
            $table->string('username');
            $table->string('password');
            $table->text('token')->nullable();
            $table->json('organs')->nullable();
            $table->json('units')->nullable();
            $table->json('sectors');
            $table->integer('profile');
            $table->json('modules')->nullable();
            $table->boolean('passchange')->default(false);
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
