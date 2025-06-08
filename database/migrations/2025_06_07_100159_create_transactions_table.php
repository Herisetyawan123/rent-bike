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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bike_id')->references('id')->on('bikes')->cascadeOnDelete();
            $table->foreignId('customer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('vendor_id')->references('id')->on('users')->cascadeOnDelete();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->decimal('paid_total')->nullable();
            $table->decimal('final_total')->default(0);
            $table->decimal('total')->default(0);
            $table->decimal('total_tax')->default(0);
            $table->enum('status', [    
                'payment_pending',
                'paid',
                'awaiting_pickup',
                'being_delivered',
                'in_use',
                'cancelled',
                'completed'])->default('payment_pending');
            $table->enum("pickup_type", ['pickup_self', 'delivery'])->default('pickup_self');
            $table->string('delivery_address')->nullable();
            $table->decimal('delivery_fee')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
