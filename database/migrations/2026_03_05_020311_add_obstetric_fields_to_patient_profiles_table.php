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
        Schema::table('patient_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('patient_profiles', 'lmp')) {
                $table->date('lmp')->nullable()->after('civil_status');
            }
            if (!Schema::hasColumn('patient_profiles', 'edd')) {
                $table->date('edd')->nullable()->after('lmp');
            }
            if (!Schema::hasColumn('patient_profiles', 'gravida')) {
                $table->integer('gravida')->nullable()->after('edd');
            }
            if (!Schema::hasColumn('patient_profiles', 'para')) {
                $table->integer('para')->nullable()->after('gravida');
            }
            if (!Schema::hasColumn('patient_profiles', 'history_miscarriage')) {
                $table->boolean('history_miscarriage')->default(false)->after('para');
            }
            if (!Schema::hasColumn('patient_profiles', 'previous_complications')) {
                $table->text('previous_complications')->nullable()->after('history_miscarriage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'lmp',
                'edd',
                'gravida',
                'para',
                'history_miscarriage',
                'previous_complications'
            ]);
        });
    }
};
