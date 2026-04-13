<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicine_supplies', function (Blueprint $table) {
            if (!Schema::hasColumn('medicine_supplies', 'disposed_at')) {
                $table->timestamp('disposed_at')->nullable()->after('received_by');
            }

            if (!Schema::hasColumn('medicine_supplies', 'disposed_by')) {
                $table->foreignId('disposed_by')->nullable()->after('disposed_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('medicine_supplies', 'disposal_notes')) {
                $table->string('disposal_notes', 500)->nullable()->after('disposed_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medicine_supplies', function (Blueprint $table) {
            if (Schema::hasColumn('medicine_supplies', 'disposed_by')) {
                $table->dropForeign(['disposed_by']);
                $table->dropColumn('disposed_by');
            }

            if (Schema::hasColumn('medicine_supplies', 'disposed_at')) {
                $table->dropColumn('disposed_at');
            }

            if (Schema::hasColumn('medicine_supplies', 'disposal_notes')) {
                $table->dropColumn('disposal_notes');
            }
        });
    }
};
