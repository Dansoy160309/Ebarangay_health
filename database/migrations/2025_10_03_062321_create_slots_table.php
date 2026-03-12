<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->string('service');         // Name of the service, e.g., vaccination
            $table->date('date');              // Date of the slot
            $table->time('start_time');        // Start time of the slot
            $table->time('end_time')->nullable(); // Optional end time
            $table->integer('capacity')->default(10); // Max appointments per slot
            $table->boolean('is_active')->default(true); // Active or inactive slot
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};

