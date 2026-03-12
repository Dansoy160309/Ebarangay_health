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
        Schema::table('users', function (Blueprint $table) {
            $table->string('civil_status')->nullable()->after('gender');
            $table->string('blood_type')->nullable()->after('civil_status');
            $table->string('philhealth_no')->nullable()->after('blood_type');
            
            $table->string('emergency_contact_name')->nullable()->after('emergency_no');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_name');
            
            $table->text('allergies')->nullable()->after('status');
            $table->text('medical_history')->nullable()->after('allergies');
            $table->text('family_history')->nullable()->after('medical_history');
            $table->text('current_medications')->nullable()->after('family_history');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'civil_status',
                'blood_type',
                'philhealth_no',
                'emergency_contact_name',
                'emergency_contact_relationship',
                'allergies',
                'medical_history',
                'family_history',
                'current_medications',
            ]);
        });
    }
};
