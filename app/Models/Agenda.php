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
}
