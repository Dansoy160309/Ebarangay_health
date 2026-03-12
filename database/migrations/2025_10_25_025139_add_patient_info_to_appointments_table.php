<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('appointments', function (Blueprint $table) {
        $table->string('patient_name')->nullable()->after('user_id');
        $table->string('patient_email')->nullable()->after('patient_name');
    });
}

public function down()
{
    Schema::table('appointments', function (Blueprint $table) {
        $table->dropColumn(['patient_name', 'patient_email']);
    });
}


};
