<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    // Arahkan ke tabel jabatan
    protected $table = 'jabatan';
    
    // Set primary key ke id_jabatan
    protected $primaryKey = 'id_jabatan';
    
    // Matikan auto-increment karena kita pakai format string (J01, J04)
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang boleh diisi
    protected $fillable = [
        'id_jabatan', 'nama_jabatan'
    ];
}
