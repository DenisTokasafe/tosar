<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Tabel Master Checklist (Pengganti array $fields di PHP)
        Schema::create('inspection_checklist_masters', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_type');
            $table->string('location_keyword')->default('Default');
            $table->json('inputs');
            $table->json('checks');
            $table->timestamps();
        });

        // 2. Tabel Sesi (Header) - Untuk menyimpan Foto Area agar tidak duplikat
        Schema::create('inspection_sessions', function (Blueprint $table) {
            $table->id();
            $table->date('inspection_date');
            $table->string('inspected_by');
            $table->string('area_name');
            $table->string('area_photo_path')->nullable();
            $table->timestamps();
        });

        // 3. Tambahkan kolom relasi ke tabel fire_protections yang sudah ada
        Schema::table('fire_protections', function (Blueprint $table) {
            $table->unsignedBigInteger('inspection_session_id')->nullable()->after('id');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_master_tables');
    }
};
