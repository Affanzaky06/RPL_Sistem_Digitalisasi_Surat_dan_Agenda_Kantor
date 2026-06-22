<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    protected $table = 'disposisi';

    protected $primaryKey = 'id_disposisi';

    protected $fillable = [
        'id_surat',
        'nip_pemberi',
        'nip_penerima',
        'tanggal',
        'catatan',
        'status'
    ];

    public function surat()
    {
        return $this->belongsTo(
            Surat::class,
            'id_surat',
            'id_surat'
        );
    }

    public function penerima()
    {
        return $this->belongsTo(
            Pegawai::class,
            'nip_penerima',
            'nip'
        );
    }

    public function pemberi()
    {
        return $this->belongsTo(
            Pegawai::class,
            'nip_pemberi',
            'nip'
        );
    }

    public function peserta()
    {
        return $this->hasOne(
            Peserta::class,
            'id_disposisi',
            'id_disposisi'
        );
    }
}
