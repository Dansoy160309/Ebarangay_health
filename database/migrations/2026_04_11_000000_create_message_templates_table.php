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
        if (Schema::hasTable('message_templates')) {
            return;
        }

        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['email', 'sms']); // email or sms
            $table->string('name'); // e.g., "Defaulter Recall Email"
            $table->string('subject')->nullable(); // for email only
            $table->longText('body'); // template with placeholders like {patient_name}
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false); // system default templates
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_templates');
    }
};
