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
        Schema::create('renters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('national_id')->nullable();        // NIK / Passport
            $table->string('driver_license_number')->nullable();  // Nomor SIM
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('ethnicity')->nullable();           // Suku
            $table->string('nationality')->nullable();
            $table->date('birth_date')->nullable();             // date (mutetable)
            $table->text('address')->nullable();
            $table->text('current_address')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('phone')->nullable();                // hp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renters');
    }
};
