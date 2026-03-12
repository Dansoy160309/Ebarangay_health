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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Unique notification ID (UUID)
            $table->string('type'); // Notification class (e.g., App\Notifications\AppointmentBookedNotification)
            $table->morphs('notifiable'); // notifiable_type + notifiable_id
            $table->text('data'); // JSON payload containing the notification message/data
            $table->timestamp('read_at')->nullable(); // When the notification was marked as read
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
