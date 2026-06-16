<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;

    protected $table = 'peserta';
    protected $primaryKey = 'id_peserta';
    protected $guarded = [];

    // Relasi ke tabel Agenda
    public function agenda()
    {
        return $this->belongsTo(Agenda::class, 'id_agenda', 'id_agenda');
    }

    // Relasi ke tabel Pegawai (NIP Peserta)
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nip');
    }

    // public function user()
    // {
    //     // Sesuaikan 'User::class' dengan nama Model user/pegawai Anda.
    //     // Asumsinya primary key di tabel user adalah 'nip', dan foreign key di peserta juga 'nip'
    //     return $this->belongsTo(User::class, 'nip', 'nip'); 
    // }
}