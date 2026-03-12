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
        Schema::table('health_records', function (Blueprint $table) {
            // Add service_id, nullable first to avoid breaking existing records if any
            // Constrained to services table
            $table->foreignId('service_id')->nullable()->after('created_by')->constrained('services')->nullOnDelete();
            
            // Add metadata for flexible storage
            $table->json('metadata')->nullable()->after('immunizations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_records', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn(['service_id', 'metadata']);
        });
    }
};
