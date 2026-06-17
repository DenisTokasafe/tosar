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
        Schema::create('mcu_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mcu_participant_id')->constrained()->cascadeOnDelete();

            // Sesuai Flowchart 1
            $table->enum('status', [
                'fit_to_work',
                'fit_with_notes',
                'temporary_unfit',
                'unfit' // Asumsi jika ada
            ]);

            $table->text('doctor_site_consult')->nullable(); // Untuk Restriction Monitoring
            $table->date('follow_up_date')->nullable(); // Untuk Follow Up MCU
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mcu_results');
    }
};
