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
        Schema::table('appointments', function (Blueprint $table) {
            // Add slot_id column
            $table->unsignedBigInteger('slot_id')->nullable()->after('service');

            // Add foreign key constraint
            $table->foreign('slot_id')->references('id')->on('slots')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['slot_id']);

            // Drop the column
            $table->dropColumn('slot_id');
        });
    }
};
