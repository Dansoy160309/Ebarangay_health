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
        // Add SMS toggle to users (for patient opt-out)
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('sms_notifications')->default(true)->after('contact_no');
        });

        // Create SMS Settings table
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create SMS Logs table
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('recipient');
            $table->text('message');
            $table->string('status'); // sent, failed, pending
            $table->string('gateway')->default('PhilSMS');
            $table->text('gateway_response')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Seed default settings
        \Illuminate\Support\Facades\DB::table('sms_settings')->insert([
            ['key' => 'sms_enabled', 'value' => '1', 'description' => 'Global toggle for all SMS notifications'],
            ['key' => 'sms_appointment_booked', 'value' => '1', 'description' => 'Send SMS when an appointment is booked'],
            ['key' => 'sms_appointment_confirmed', 'value' => '1', 'description' => 'Send SMS when an appointment is confirmed'],
            ['key' => 'sms_appointment_reminders', 'value' => '1', 'description' => 'Send SMS for appointment reminders'],
            ['key' => 'sms_appointment_cancelled', 'value' => '1', 'description' => 'Send SMS when an appointment is cancelled'],
            ['key' => 'sms_defaulter_recall', 'value' => '1', 'description' => 'Send SMS for manual defaulter recall'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sms_notifications');
        });
        Schema::dropIfExists('sms_settings');
        Schema::dropIfExists('sms_logs');
    }
};
