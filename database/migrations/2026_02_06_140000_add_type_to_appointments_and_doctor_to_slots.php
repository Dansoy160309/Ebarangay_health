<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('type', ['scheduled', 'walk-in'])->default('scheduled')->after('service');
        });

        Schema::table('slots', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null')->after('service');
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('slots', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropColumn('doctor_id');
        });
    }
};
