<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'surat';

    protected $primaryKey = 'id_surat';

    protected $fillable = [
        'perihal',
        'nomor_surat',
        'jenis_surat',
        'prioritas',
        'tanggal_surat',
        'tanggal_kegiatan',
        'lokasi_kegiatan',
        'waktu_mulai_kegiatan',
        'waktu_selesai_kegiatan',
        'asal_surat',
        'status',
        'file_scan',
        'tanggal_verifikasi'
    ];



    public function agenda()
    {
        return $this->hasOne(Agenda::class, 'id_surat', 'id_surat');
    }


    public function disposisi()
    {
        // Parameter kedua adalah foreign key di tabel disposisi
        // Parameter ketiga adalah primary key di tabel surat
        return $this->hasMany(Disposisi::class, 'id_surat', 'id_surat');
    }
}
