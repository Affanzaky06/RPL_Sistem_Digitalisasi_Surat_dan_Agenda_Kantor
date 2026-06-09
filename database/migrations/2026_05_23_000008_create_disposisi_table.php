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
        Schema::create('disposisi', function (Blueprint $table) {
            $table->id('id_disposisi');
            $table->unsignedBigInteger('id_surat');
            $table->string('nip_pemberi');
            $table->string('nip_penerima');
            $table->dateTime('tanggal');
            $table->text('catatan');
            $table->string('status');
            $table->foreign('id_surat')
                ->references('id_surat')
                ->on('surat');
            $table->foreign('nip_pemberi')
                ->references('nip')
                ->on('pegawai');
            $table->foreign('nip_penerima')
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
        Schema::dropIfExists('disposisi');
    }
};
