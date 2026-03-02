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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique(); // Contoh: INC-2026-0001
            $table->foreignId('penanggung_jawab_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pelapor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('manualPelaporName')->nullable(); // path file
            $table->dateTime('incident_datetime');
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('location_specific')->nullable();
            $table->foreignId('event_type_id')->constrained('event_types')->cascadeOnDelete();
            $table->foreignId('event_sub_type_id')->nullable()->constrained('event_sub_types')->cascadeOnDelete();
            // Keyword (kta/tta)
            $table->enum('key_word', ['kta', 'tta']);
            $table->foreignId('kondisi_tidak_aman_id')->nullable()->constrained('unsafe_conditions')->nullOnDelete();
            $table->foreignId('tindakan_tidak_aman_id')->nullable()->constrained('unsafe_acts')->nullOnDelete();

            // Deskripsi & dokumen
            $table->longText('description')->nullable();
            $table->string('doc_deskripsi')->nullable(); // path file
            $table->longText('immediate_corrective_action')->nullable();
            $table->string('doc_corrective')->nullable(); // path file

            // Risk matrix
            $table->foreignId('consequence_id')->nullable()->constrained('risk_consequences')->nullOnDelete();
            $table->foreignId('likelihood_id')->nullable()->constrained('likelihoods')->nullOnDelete();
            $table->string('risk_level')->nullable(); // Low, Moderate, High, Extreme
            $table->string('unit_involved')->nullable(); // No. Lambung Alat
            $table->enum('status', ['submitted', 'in_progress', 'pending', 'closed', 'cancelled'])->default('submitted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
