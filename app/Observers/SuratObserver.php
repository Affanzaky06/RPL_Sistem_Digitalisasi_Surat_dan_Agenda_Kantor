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

    public function updated(Surat $surat)
    {
        if ($surat->isDirty('status')) {
            $sekretarisList = Pegawai::whereIn('id_jabatan', ['J006', 'J007'])->get();
            
            if ($surat->status === 'Ditolak Kepala') {
                foreach ($sekretarisList as $sekretaris) {
                    $sekretaris->notify(new SistemNotification(
                        'Surat Ditolak',
                        'Surat dari ' . $surat->asal_surat . ' (Perihal: ' . $surat->perihal . ') ditolak oleh Kepala Kantor.',
                        '#'
                    ));
                }
            } elseif ($surat->status === 'Terverifikasi') {
                foreach ($sekretarisList as $sekretaris) {
                    $sekretaris->notify(new SistemNotification(
                        'Surat Terverifikasi',
                        'Surat dari ' . $surat->asal_surat . ' (Perihal: ' . $surat->perihal . ') telah diverifikasi oleh Kepala Kantor.',
                        '#'
                    ));
                }
            }
        }
    }
}
