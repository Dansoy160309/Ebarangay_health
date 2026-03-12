<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medicine_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('midwife_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('dosage');
            $table->string('frequency');
            $table->string('duration');
            $table->text('notes')->nullable();
            $table->timestamp('distributed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_distributions');
    }
};

