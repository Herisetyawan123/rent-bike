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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
                        // Relasi ke tabel users
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Data Karyawan
            $table->string('employee_id')->unique(); // NIP atau ID Pegawai
            $table->string('position');              // Jabatan
            $table->string('department')->nullable();
            $table->enum('employment_status', ['contract', 'permanent', 'intern'])->default('contract');
            $table->date('join_date')->nullable();
            $table->date('resign_date')->nullable();

            // Informasi Tambahan (opsional)
            $table->string('phone')->nullable();
            $table->string('national_id')->nullable(); // NIK
            $table->text('address')->nullable();
            $table->string('photo')->nullable();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
