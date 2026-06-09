<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat', function (Blueprint $table) {
            $table->id('id_surat');
            $table->text('perihal');
            $table->string('nomor_surat')->unique();
            $table->string('jenis_surat');
            $table->string('prioritas')->nullable();
            $table->date('tanggal_surat');
            $table->date('tanggal_kegiatan')->nullable();
            $table->string('lokasi_kegiatan')->nullable();
            $table->time('waktu_mulai_kegiatan')->nullable();
            $table->time('waktu_selesai_kegiatan')->nullable();
            $table->string('asal_surat');
            $table->string('status')->default('Menunggu Verifikasi');
            $table->string('file_scan');
            $table->dateTime('tanggal_verifikasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat');
    }
};
