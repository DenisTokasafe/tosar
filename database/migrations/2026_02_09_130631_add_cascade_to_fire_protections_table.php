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
    Schema::table('fire_protections', function (Blueprint $table) {
        // 1. Hapus foreign key yang lama (sesuaikan nama kolomnya)
        $table->dropForeign(['inspection_session_id']);

        // 2. Tambahkan kembali dengan cascade delete
        $table->foreign('inspection_session_id')
              ->references('id')
              ->on('inspection_sessions')
              ->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('fire_protections', function (Blueprint $table) {
        $table->dropForeign(['inspection_session_id']);

        // Kembalikan ke semula (tanpa cascade) jika rollback
        $table->foreign('inspection_session_id')
              ->references('id')
              ->on('inspection_sessions');
    });
}
