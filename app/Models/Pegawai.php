<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pegawai extends Authenticatable
{
    use Notifiable;

    protected $table = 'pegawai';
    protected $primaryKey = 'nip';
    public $incrementing = false; // Karena NIP bukan auto-increment integer
    protected $keyType = 'string';

    protected $fillable = [
        'nip', 'nama', 'password', 'id_bidang', 'id_jabatan', 'nip_atasan'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
