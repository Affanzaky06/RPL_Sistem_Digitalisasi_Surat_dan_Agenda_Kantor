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
        Schema::create('peserta', function (Blueprint $table) {
            $table->id('id_peserta');

            $table->unsignedBigInteger('id_disposisi')->nullable();
            $table->unsignedBigInteger('id_agenda');

            $table->string('nip');
            $table->string('status_kehadiran');

            $table->foreign('id_disposisi')
                ->references('id_disposisi')
                ->on('disposisi');

            $table->foreign('id_agenda')
                ->references('id_agenda')
                ->on('agenda');

            $table->foreign('nip')
                ->references('nip')
                ->on('pegawai');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};
