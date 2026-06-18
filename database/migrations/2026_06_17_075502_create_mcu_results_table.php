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

            // --- 1. DATA DARI MEDICAL ADMIN ---
            $table->string('result_document')->nullable(); // Menyimpan path file PDF/Foto
            $table->text('admin_notes')->nullable(); // Catatan tambahan dari admin saat upload

            // --- 2. STATUS ALUR KERJA (WORKFLOW) ---
            $table->enum('workflow_status', [
                'pending_doctor', // Saat admin selesai upload
                'reviewed'        // Saat dokter selesai review
            ])->default('pending_doctor');

            // --- 3. DATA REVIEW DARI DOCTOR ---
            // Dibuat nullable karena dokter belum mengisi saat dokumen baru diupload admin
            $table->enum('status', [
                'fit_to_work',
                'fit_with_notes',
                'temporary_unfit',
                'unfit'
            ])->nullable();

            $table->text('doctor_site_consult')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users'); // ID Dokter yang review

            // --- 4. STATUS PUBLISH ---
            $table->boolean('is_published')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcu_results');
    }
};
