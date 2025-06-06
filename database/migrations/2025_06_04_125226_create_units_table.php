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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Hari, Minggu, Bulan
            $table->unsignedBigInteger('parent_id')->nullable(); // refer ke unit_id
            $table->integer('multiplier')->nullable(); // misal: Minggu = 7 Hari

            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
