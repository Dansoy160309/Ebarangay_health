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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('guardian_id')->nullable()->after('id');
            $table->string('relationship')->nullable()->after('guardian_id');
            $table->string('dependency_reason')->nullable()->after('relationship');
            
            $table->foreign('guardian_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['guardian_id']);
            $table->dropColumn(['guardian_id', 'relationship', 'dependency_reason']);
        });
    }
};
