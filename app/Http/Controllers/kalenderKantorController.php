<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Surat;
use App\Models\Peserta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KalenderKantorController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $roleMap = [
            'J001' => 'Kepala',
            'J002' => 'Kabid',
            'J003' => 'Subkoor',
            'J004' => 'Staff',
            'J005' => 'Kepegawaian',
            'J006' => 'Sekretaris',
            'J007' => 'Frontliner'
        ];
        
        $role = $roleMap[$user->id_jabatan] ?? 'Umum';

        // 1. Daftar semua pegawai untuk dropdown filter (format: "Nama - Jabatan")
        $daftarStaff = Pegawai::with('jabatan')
            ->orderBy('nama')
            ->get()
            ->map(function ($pegawai) {
                return [
                    'nip' => $pegawai->nip,
                    'label' => $pegawai->nama . ' - ' . ($pegawai->jabatan->nama_jabatan ?? 'Umum'),
                ];
            });

        // 2. Tarik Data Utama untuk FullCalendar
        $agendaList = Surat::with(['disposisi.penerima.jabatan', 'disposisi.pemberi.jabatan'])
            ->whereNotNull('tanggal_kegiatan')
            ->where('status', 'Terverifikasi')
            ->get();

        $events = $agendaList->map(function ($item) {
            $kegiatanDate = Carbon::parse($item->tanggal_kegiatan);
            $statusAcara = 'mendatang';

            if ($kegiatanDate->isPast() && !Carbon::today()->isSameDay($kegiatanDate)) {
                $statusAcara = 'terlaksana';
            } elseif (Carbon::today()->isSameDay($kegiatanDate)) {
                $statusAcara = 'berlangsung';
            }

            // Kumpulkan NIP semua pegawai yang terlibat (pemberi + penerima disposisi)
            $nipPenerima = $item->disposisi->pluck('nip_penerima')->filter();
            $nipPemberi = $item->disposisi->pluck('nip_pemberi')->filter();
            $semuaNipTerlibat = $nipPenerima->merge($nipPemberi)
                ->unique()
                ->values()
                ->toArray();

            // Kumpulkan label nama-jabatan pegawai yang terlibat
            $labelStaff = collect();
            foreach ($item->disposisi as $d) {
                if ($d->penerima) {
                    $labelStaff->push($d->penerima->nama . ' - ' . ($d->penerima->jabatan->nama_jabatan ?? 'Umum'));
                }
                if ($d->pemberi) {
                    $labelStaff->push($d->pemberi->nama . ' - ' . ($d->pemberi->jabatan->nama_jabatan ?? 'Umum'));
                }
            }
            $labelStaff = $labelStaff->unique()->values()->toArray();
                
            return [
                'id' => $item->id_surat,
                'title' => $item->perihal,
                'start' => $item->tanggal_kegiatan . 'T' . $item->waktu_mulai_kegiatan,
                'end' => $item->tanggal_kegiatan . 'T' . $item->waktu_selesai_kegiatan,
                'extendedProps' => [
                    'lokasi' => $item->lokasi_kegiatan,
                    'status' => $statusAcara,
                    'daftar_staff' => $semuaNipTerlibat,
                    'daftar_staff_label' => $labelStaff,
                ]
            ];
        });

        // 3. Logika Cerdas untuk Card Sidebar (Ringkasan Agenda 3 Terdekat)
        if (in_array($user->id_jabatan, ['J005', 'J007'])) {
            $ringkasanAgenda = Surat::select(
                    'id_surat as id_agenda',
                    'perihal as nama_kegiatan',
                    'tanggal_kegiatan',
                    'waktu_mulai_kegiatan as waktu_mulai',
                    'nomor_surat'
                )
                ->whereNotNull('tanggal_kegiatan')
                ->where('status', 'Terverifikasi')
                ->whereDate('tanggal_kegiatan', '>=', Carbon::today())
                ->orderBy('tanggal_kegiatan', 'asc')
                ->orderBy('waktu_mulai_kegiatan', 'asc')
                ->take(3)
                ->get();
        } else {
            $ringkasanAgenda = Peserta::join('agenda', 'peserta.id_agenda', '=', 'agenda.id_agenda')
                ->join('surat', 'agenda.id_surat', '=', 'surat.id_surat')
                ->select(
                    'agenda.id_agenda',
                    'agenda.nama_kegiatan',
                    'agenda.tanggal_kegiatan',
                    'agenda.waktu_mulai',
                    'surat.nomor_surat'
                )
                ->where('peserta.nip', $user->nip)
                ->where('peserta.status_kehadiran', 'Hadir')
                ->whereDate('agenda.tanggal_kegiatan', '>=', Carbon::today())
                ->orderBy('agenda.tanggal_kegiatan', 'asc')
                ->orderBy('agenda.waktu_mulai', 'asc')
                ->distinct()
                ->take(3)
                ->get();
        }

        return view('KalenderKantor', [
            'title' => $role,
            'role' => $role,
            'events' => $events,
            'daftarStaff' => $daftarStaff,
            'ringkasanAgenda' => $ringkasanAgenda
        ]);
    }
}
