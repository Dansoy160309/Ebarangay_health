<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccine_administrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vaccine_id')->constrained('vaccines');
            $table->foreignId('vaccine_batch_id')->constrained('vaccine_batches');
            $table->foreignId('patient_id')->constrained('users');
            $table->foreignId('administered_by')->constrained('users');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments');

            $table->unsignedInteger('quantity')->default(1);
            $table->dateTime('administered_at');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['appointment_id', 'vaccine_batch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccine_administrations');
    }
};

