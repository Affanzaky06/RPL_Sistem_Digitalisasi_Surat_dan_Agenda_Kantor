<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pegawai extends Authenticatable
{
    use Notifiable;
    use HasFactory;

    protected $table = 'pegawai';
    protected $primaryKey = 'nip';
    public $incrementing = false; // Karena NIP bukan auto-increment integer
    protected $keyType = 'string';

    protected $fillable = [
        'nip',
        'nama',
        'password',
        'id_bidang',
        'id_jabatan',
        'nip_atasan'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function jabatan()
    {
        // Parameter: (NamaModelTarget, foreign_key_di_tabel_pegawai, primary_key_di_tabel_jabatan)
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }



    public function atasan()
    {
        return $this->belongsTo(
            Pegawai::class,
            'nip_atasan',
            'nip'
        );
    }

    public function bawahan()
    {
        return $this->hasMany(
            Pegawai::class,
            'nip_atasan',
            'nip'
        );
    }

    public function bidang()
    {
        return $this->belongsTo(
            Bidang::class,
            'id_bidang',
            'id_bidang'
        );
    }
}
