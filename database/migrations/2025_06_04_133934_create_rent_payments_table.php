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
        Schema::create('rent_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('renter_id')->constrained()->cascadeOnDelete();  // penyewa
            $table->foreignId('rent_bike_id')->constrained()->cascadeOnDelete(); // motor yang disewa
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete(); // vendor pemilik motor
            $table->foreignId('package_id')->nullable()->constrained('bike_packages')->nullOnDelete(); // paket sewa, optional
            $table->date('rent_start_date');
            $table->date('rent_end_date');
            $table->decimal('total_price', 10, 2);
            $table->enum('payment_status', ['pending', 'paid', 'unpaid', 'failed'])->default('pending');
            $table->enum('payment_method', ['bank_transfer', 'cash', 'online'])->nullable();
            $table->string('payment_proof')->nullable(); // misal foto bukti transfer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_payments');
    }
};
