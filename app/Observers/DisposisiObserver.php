<?php

namespace App\Observers;

use App\Models\Disposisi;
use App\Models\Pegawai;
use App\Models\Surat;
use App\Notifications\SistemNotification;

class DisposisiObserver
{
    public function created(Disposisi $disposisi)
    {
        // Beri tahu penerima
        $penerima = Pegawai::find($disposisi->nip_penerima);
        if ($penerima && in_array($disposisi->status, ['Menunggu Konfirmasi', 'Belum Dibaca'])) {
            $surat = Surat::find($disposisi->id_surat);
            $perihal = $surat ? $surat->perihal : 'Surat Baru';
            
            $penerima->notify(new SistemNotification(
                'Disposisi / Undangan Baru Masuk',
                'Perihal: ' . $perihal . '. Catatan: ' . $disposisi->catatan,
                '#'
            ));
        }
    }

    public function updated(Disposisi $disposisi)
    {
        if ($disposisi->isDirty('status') || $disposisi->isDirty('catatan')) {
            $pemberi = Pegawai::find($disposisi->nip_pemberi);
            $penerima = Pegawai::find($disposisi->nip_penerima);
            $surat = Surat::find($disposisi->id_surat);
            $perihal = $surat ? $surat->perihal : 'Surat';

            $namaPenerima = $penerima ? $penerima->nama : 'Pegawai';
            $namaPemberi = $pemberi ? $pemberi->nama : 'Atasan';

            switch ($disposisi->status) {
                case 'Menunggu Konfirmasi':
                    if ($penerima) {
                        if (str_contains($disposisi->catatan, 'Atasan batal hadir')) {
                            $penerima->notify(new SistemNotification(
                                'Peran Diubah: Perwakilan',
                                $namaPemberi . ' batal hadir dan menunjuk Anda sebagai perwakilan untuk (Perihal: ' . $perihal . '). Silakan konfirmasi kehadiran.',
                                '#'
                            ));
                        } else if ($disposisi->isDirty('status')) {
                            $penerima->notify(new SistemNotification(
                                'Disposisi / Undangan Baru Masuk',
                                'Perihal: ' . $perihal . '. Catatan: ' . $disposisi->catatan,
                                '#'
                            ));
                        }
                    }
                    break;

                case 'Tidak Hadir':
                case 'Ditolak':
                    if ($pemberi) {
                        $pemberi->notify(new SistemNotification(
                            'Disposisi Ditolak',
                            $namaPenerima . ' menolak surat/undangan (Perihal: ' . $perihal . '). Silakan cek Laporan Pemantauan.',
                            '#'
                        ));
                    }
                    break;

                case 'Hadir':
                    if ($pemberi) {
                        $pemberi->notify(new SistemNotification(
                            'Konfirmasi Kehadiran',
                            $namaPenerima . ' telah menyanggupi kehadiran untuk (Perihal: ' . $perihal . ').',
                            '#'
                        ));
                    }
                    break;

                case 'Dibatalkan':
                case 'Digantikan':
                    if ($penerima) {
                        $penerima->notify(new SistemNotification(
                            'Disposisi Dibatalkan',
                            'Disposisi untuk surat (Perihal: ' . $perihal . ') telah dibatalkan atau dialihkan oleh ' . $namaPemberi . '.',
                            '#'
                        ));
                    }
                    break;
                case 'Dimaklumi':
                    if ($penerima) {
                        $penerima->notify(new SistemNotification(
                            'Penolakan Disetujui',
                            'Penolakan Anda untuk (Perihal: ' . $perihal . ') telah dimaklumi oleh ' . $namaPemberi . '.',
                            '#'
                        ));
                    }
                    break;
            }
        }
    }
}
