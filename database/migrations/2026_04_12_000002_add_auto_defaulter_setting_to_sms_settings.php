<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('sms_settings')->updateOrInsert(
            ['key' => 'sms_auto_defaulter_first_reminder'],
            [
                'value' => '0',
                'description' => 'Enable automatic first SMS reminder for defaulters',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('sms_settings')
            ->where('key', 'sms_auto_defaulter_first_reminder')
            ->delete();
    }
};
