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
            $table->string('id_surat')->primary();
            $table->string('nomor_surat');
            $table->string('prioritas')->nullable();
            $table->date('tanggal_surat');
            $table->string('asal_surat');
            $table->text('perihal');
            $table->string('status');
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
