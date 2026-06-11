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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->string('nip')->primary();
            $table->string('nama');
            $table->date('tanggal_lahir');
            $table->string('password');
            $table->string('id_bidang')->nullable();
            $table->string('id_jabatan')->nullable();
            $table->string('nip_atasan')->nullable();
            $table->string('foto_profil')->nullable();

            // Foreign Keys
            $table->foreign('id_bidang')->references('id_bidang')->on('bidang');
            $table->foreign('id_jabatan')->references('id_jabatan')->on('jabatan');
            $table->foreign('nip_atasan')->references('nip')->on('pegawai');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
