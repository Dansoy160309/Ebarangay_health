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
            // Maternal Health Fields
            $table->date('lmp')->nullable()->comment('Last Menstrual Period');
            $table->date('edd')->nullable()->comment('Estimated Date of Delivery');
            $table->unsignedInteger('gravida')->nullable()->comment('Number of pregnancies');
            $table->unsignedInteger('para')->nullable()->comment('Number of births');
            $table->boolean('is_high_risk')->default(false);
            $table->text('maternal_risk_notes')->nullable();
            
            // Child Health / Immunization
            $table->boolean('is_fully_immunized')->default(false)->comment('FIC - Fully Immunized Child');
            $table->date('last_immunization_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'lmp', 'edd', 'gravida', 'para', 
                'is_high_risk', 'maternal_risk_notes',
                'is_fully_immunized', 'last_immunization_date'
            ]);
        });
    }
};
