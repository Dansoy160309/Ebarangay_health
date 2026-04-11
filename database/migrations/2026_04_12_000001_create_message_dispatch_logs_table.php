<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_dispatch_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->foreignId('patient_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sender_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('message_templates')->nullOnDelete();
            $table->string('category')->default('defaulter_recall');
            $table->string('channel'); // sms|email
            $table->unsignedTinyInteger('stage')->default(1); // 1,2,3
            $table->string('trigger_mode')->default('manual'); // manual|auto
            $table->string('status')->default('sent'); // sent|failed|skipped
            $table->string('recipient')->nullable();
            $table->text('reason')->nullable();
            $table->text('provider_response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['category', 'status', 'created_at']);
            $table->index(['appointment_id', 'channel', 'stage']);
            $table->index(['trigger_mode', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_dispatch_logs');
    }
};
