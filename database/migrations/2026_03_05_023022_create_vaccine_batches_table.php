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
        Schema::create('vaccine_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vaccine_id')->constrained()->onDelete('cascade');
            $table->string('batch_number');
            $table->date('expiry_date');
            $table->integer('quantity_received');
            $table->integer('quantity_administered')->default(0);
            $table->integer('quantity_remaining');
            $table->date('received_at');
            $table->string('supplier')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index for faster stock lookups
            $table->index(['vaccine_id', 'expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccine_batches');
    }
};
