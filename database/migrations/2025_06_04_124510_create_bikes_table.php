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
        Schema::create('bikes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bike_merk_id')->references('id')->on('bike_merks')->onDelete('cascade');
            $table->foreignId('bike_type_id')->references('id')->on('bike_types')->onDelete(action: 'cascade');
            $table->foreignId('bike_color_id')->references('id')->on('bike_colors')->onDelete('cascade');
            $table->foreignId('bike_capacity_id')->references('id')->on('bike_capacities')->onDelete('cascade');
            $table->year('year');
            $table->string('license_plate')->unique();
            $table->decimal('price', 10, 2);
            $table->enum('availability_status', ['available', 'rented'])->default('available');
            $table->enum('status', ['requested', 'accepted'])->default('requested');
            $table->string('photo')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_bikes');
    }
};
