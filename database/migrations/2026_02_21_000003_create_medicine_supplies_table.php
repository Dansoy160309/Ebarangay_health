<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medicine_supplies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('batch_number')->nullable();
            $table->unsignedInteger('quantity');
            $table->date('expiration_date')->nullable();
            $table->string('supplier_name')->nullable();
            $table->date('date_received');
            $table->foreignId('received_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_supplies');
    }
};

