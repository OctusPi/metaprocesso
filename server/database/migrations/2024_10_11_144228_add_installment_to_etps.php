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
        Schema::table('etps', function (Blueprint $table) {
            $table->integer('installment_type');
            $table->text('installment_justification')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etps', function (Blueprint $table) {
            $table->dropColumn('installment_type');
            $table->dropColumn('installment_justification');
        });
    }
};
