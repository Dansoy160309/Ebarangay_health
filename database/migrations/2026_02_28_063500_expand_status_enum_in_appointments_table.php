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
            // Add all statuses used across the app:
            // pending, approved, rescheduled, completed, cancelled, rejected, no_show, archived
            $table->enum('status', [
                'pending',
                'approved',
                'rescheduled',
                'completed',
                'cancelled',
                'rejected',
                'no_show',
                'archived',
            ])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Revert to the previously known set (keep the more conservative set)
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'cancelled',
                'completed',
            ])->default('pending')->change();
        });
    }
};

