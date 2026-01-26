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
        Schema::create('fire_protections', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Contoh: 'Fire Extinguisher', 'Hydrant', 'Eye Wash'
            $table->string('location');
            $table->string('equipment_no')->nullable();
            $table->date('inspection_date');
            $table->string('inspected_by');
            // Menggunakan JSON untuk menyimpan checklist kondisi agar fleksibel
            // (Karena tiap alat punya kriteria checklist berbeda seperti 'Nozzle', 'Pressure', dll)
            $table->json('conditions')->nullable();
            $table->text('remarks')->nullable();
            $table->string('documentation_path')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fire_protections');
    }
};
