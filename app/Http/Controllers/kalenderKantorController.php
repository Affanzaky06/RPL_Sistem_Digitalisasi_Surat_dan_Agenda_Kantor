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
        $agendaList = \App\Models\Agenda::with(['surat', 'peserta.pegawai.jabatan'])->get();

        $events = $agendaList->map(function ($item) {
            $surat = $item->surat;
            $kegiatanDate = Carbon::parse($item->tanggal_kegiatan);
            $now = Carbon::now();
            $mulai = Carbon::parse($item->tanggal_kegiatan . ' ' . $item->waktu_mulai);
            $selesai = Carbon::parse($item->tanggal_kegiatan . ' ' . $item->waktu_selesai);
            
            if ($now->gt($selesai)) {
                $statusAcara = 'terlaksana';
            } elseif ($now->gte($mulai) && $now->lte($selesai)) {
                $statusAcara = 'berlangsung';
            } else {
                $statusAcara = 'mendatang';
            }

            // Kumpulkan NIP dan Label pegawai yang hadir/perwakilan saja
            $semuaNipTerlibat = [];
            $labelStaff = [];

            foreach ($item->peserta as $p) {
                if (in_array($p->status_kehadiran, ['Hadir', 'Perwakilan'])) {
                    $semuaNipTerlibat[] = $p->nip;
                    $labelStaff[] = ($p->pegawai->nama ?? $p->nip) . ' - ' . ($p->pegawai->jabatan->nama_jabatan ?? 'Umum');
                }
            }

            $semuaNipTerlibat = array_values(array_unique($semuaNipTerlibat));
            $labelStaff = array_values(array_unique($labelStaff));
                
            return [
                'id' => $item->id_agenda,
                'title' => $item->nama_kegiatan ?? ($surat->perihal ?? '-'),
                'start' => $item->tanggal_kegiatan . 'T' . $item->waktu_mulai,
                'end' => $item->tanggal_kegiatan . 'T' . $item->waktu_selesai,
                'extendedProps' => [
                    'lokasi' => $item->lokasi,
                    'status' => $statusAcara,
                    'daftar_staff' => $semuaNipTerlibat,
                    'daftar_staff_label' => $labelStaff,
                ]
            ];
        });

        // 3. Logika Cerdas untuk Card Sidebar (Ringkasan Agenda 3 Terdekat)
        if (in_array($user->id_jabatan, ['J005', 'J007'])) {
            $ringkasanAgenda = \App\Models\Agenda::join('surat', 'agenda.id_surat', '=', 'surat.id_surat')
                ->select(
                    'agenda.id_agenda',
                    'agenda.nama_kegiatan',
                    'agenda.tanggal_kegiatan',
                    'agenda.waktu_mulai',
                    'surat.nomor_surat'
                )
                ->where(function ($query) {
                $query->whereDate('agenda.tanggal_kegiatan', '>', \Carbon\Carbon::today())
                      ->orWhere(function ($q) {
                          $q->whereDate('agenda.tanggal_kegiatan', '=', \Carbon\Carbon::today())
                            ->whereTime('agenda.waktu_selesai', '>', \Carbon\Carbon::now()->format('H:i:s'));
                      });
            })
                ->orderBy('agenda.tanggal_kegiatan', 'asc')
                ->orderBy('agenda.waktu_mulai', 'asc')
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
                ->where(function ($query) {
                $query->whereDate('agenda.tanggal_kegiatan', '>', \Carbon\Carbon::today())
                      ->orWhere(function ($q) {
                          $q->whereDate('agenda.tanggal_kegiatan', '=', \Carbon\Carbon::today())
                            ->whereTime('agenda.waktu_selesai', '>', \Carbon\Carbon::now()->format('H:i:s'));
                      });
            })
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
