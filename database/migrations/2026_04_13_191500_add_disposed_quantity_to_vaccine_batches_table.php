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
        Schema::table('vaccine_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('vaccine_batches', 'disposed_quantity')) {
                $table->unsignedInteger('disposed_quantity')->nullable()->after('disposed_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaccine_batches', function (Blueprint $table) {
            if (Schema::hasColumn('vaccine_batches', 'disposed_quantity')) {
                $table->dropColumn('disposed_quantity');
            }
        });
    }
};
