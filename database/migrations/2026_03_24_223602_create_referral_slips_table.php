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
        Schema::create('referral_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('midwife_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->string('family_no')->nullable();
            
            // Referral details
            $table->string('referred_from'); // Store as comma-separated or JSON
            $table->string('referred_from_other')->nullable();
            $table->string('referred_to'); // Store as comma-separated or JSON
            $table->string('referred_to_other')->nullable();
            
            // Content
            $table->text('pertinent_findings')->nullable();
            $table->text('reason_for_referral')->nullable();
            $table->text('instruction_to_referring_level')->nullable();
            $table->text('actions_taken_by_referred_level')->nullable();
            $table->text('instructions_to_referring_level_final')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_slips');
    }
};
