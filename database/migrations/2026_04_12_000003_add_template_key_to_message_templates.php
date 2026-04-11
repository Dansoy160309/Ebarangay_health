<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('message_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('message_templates', 'template_key')) {
                $table->string('template_key')->nullable()->after('type');
            }
        });

        DB::table('message_templates')
            ->where('name', 'Defaulter Recall Email')
            ->update(['template_key' => 'defaulter_recall_email']);

        DB::table('message_templates')
            ->where('name', 'Defaulter Recall SMS')
            ->update(['template_key' => 'defaulter_recall_sms']);

        DB::table('message_templates')
            ->where('name', 'Appointment Reminder Email')
            ->update(['template_key' => 'appointment_reminder_email']);

        DB::table('message_templates')
            ->where('name', 'Appointment Reminder SMS')
            ->update(['template_key' => 'appointment_reminder_sms']);

        Schema::table('message_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('message_templates', 'template_key')) {
                return;
            }

            $table->unique('template_key');
        });
    }

    public function down(): void
    {
        Schema::table('message_templates', function (Blueprint $table) {
            if (Schema::hasColumn('message_templates', 'template_key')) {
                $table->dropUnique(['template_key']);
                $table->dropColumn('template_key');
            }
        });
    }
};
