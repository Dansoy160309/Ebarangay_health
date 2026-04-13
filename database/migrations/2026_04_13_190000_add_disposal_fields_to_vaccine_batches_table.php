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
            if (!Schema::hasColumn('vaccine_batches', 'disposed_at')) {
                $table->timestamp('disposed_at')->nullable()->after('supplier');
            }

            if (!Schema::hasColumn('vaccine_batches', 'disposed_by')) {
                $table->foreignId('disposed_by')->nullable()->after('disposed_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('vaccine_batches', 'disposed_quantity')) {
                $table->unsignedInteger('disposed_quantity')->nullable()->after('disposed_by');
            }

            if (!Schema::hasColumn('vaccine_batches', 'disposal_notes')) {
                $table->text('disposal_notes')->nullable()->after('disposed_quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaccine_batches', function (Blueprint $table) {
            if (Schema::hasColumn('vaccine_batches', 'disposed_by')) {
                $table->dropConstrainedForeignId('disposed_by');
            }

            if (Schema::hasColumn('vaccine_batches', 'disposal_notes')) {
                $table->dropColumn('disposal_notes');
            }

            if (Schema::hasColumn('vaccine_batches', 'disposed_quantity')) {
                $table->dropColumn('disposed_quantity');
            }

            if (Schema::hasColumn('vaccine_batches', 'disposed_at')) {
                $table->dropColumn('disposed_at');
            }
        });
    }
};