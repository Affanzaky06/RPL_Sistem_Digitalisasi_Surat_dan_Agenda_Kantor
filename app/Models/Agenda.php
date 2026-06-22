<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'agenda';

    protected $primaryKey = 'id_agenda';

    protected $fillable = [
        'id_surat',
        'nama_kegiatan',
        'tanggal_kegiatan',
        'lokasi',
        'waktu_mulai',
        'waktu_selesai'
    ];

    public function surat()
    {
        return $this->belongsTo(Surat::class, 'id_surat', 'id_surat');
    }

    public function peserta()
    {
        // Sesuaikan 'id_agenda' jika nama primary key Anda berbeda
        return $this->hasMany(Peserta::class, 'id_agenda', 'id_agenda'); 
    }

    /**
     * Mengecek apakah user memiliki jadwal bentrok
     */
    public static function checkConflict($nip, $tanggal_kegiatan, $waktu_mulai, $waktu_selesai)
    {
        if (!$tanggal_kegiatan || !$waktu_mulai || !$waktu_selesai) {
            return null;
        }

        return self::whereHas('peserta', function($query) use ($nip) {
                $query->where('nip', $nip)
                      ->where('status_kehadiran', 'Hadir');
            })
            ->where('tanggal_kegiatan', $tanggal_kegiatan)
            ->where(function($query) use ($waktu_mulai, $waktu_selesai) {
                // Rentang waktu bersinggungan jika:
                // waktu mulai acara lama < waktu selesai acara baru 
                // DAN waktu selesai acara lama > waktu mulai acara baru
                $query->where('waktu_mulai', '<', $waktu_selesai)
                      ->where('waktu_selesai', '>', $waktu_mulai);
            })
            ->first();
    }
}

