<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcu_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mcu_participant_id')->constrained()->cascadeOnDelete();

            // 1. Tambahkan kolom untuk dokumen yang diupload admin
            $table->string('result_document')->nullable();
            $table->text('admin_notes')->nullable();

            // 2. Tambahkan status alur kerja (workflow)
            $table->enum('workflow_status', [
                'pending_doctor', // Baru diupload admin, menunggu dokter
                'reviewed'        // Sudah selesai direview dokter
            ])->default('pending_doctor');

            // 3. Status Medis dari Dokter (Buat NULLABLE karena diisi belakangan)
            $table->enum('status', [
                'fit_to_work',
                'fit_with_notes',
                'temporary_unfit',
                'unfit'
            ])->nullable(); // <--- WAJIB NULLABLE

            $table->text('doctor_site_consult')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcu_results');
    }
};
