<?php

namespace App\Observers;

use App\Models\Surat;
use App\Models\Pegawai;
use App\Notifications\SistemNotification;

class SuratObserver
{
    public function created(Surat $surat)
    {
        // Beritahu Kepala Kantor ada surat baru menunggu verifikasi
        if ($surat->status === 'Menunggu Verifikasi') {
            $kepalaList = Pegawai::whereIn('id_jabatan', ['J001', 'J006'])->get();
            foreach ($kepalaList as $kepala) {
                $kepala->notify(new SistemNotification(
                    'Surat Baru Menunggu Verifikasi',
                    'Ada surat baru dari ' . $surat->asal_surat . ' perihal: ' . $surat->perihal,
                    '#'
                ));
            }
        }
    }
}
