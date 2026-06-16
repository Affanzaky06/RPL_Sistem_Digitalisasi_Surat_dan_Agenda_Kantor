<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\Peserta; // Tambahkan ini
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

        // 1. Tarik Data Utama untuk FullCalendar
        $agendaList = Surat::with('disposisi')
            ->whereNotNull('tanggal_kegiatan')
            ->where('status', 'Terverifikasi')
            ->get();

        $daftarStaff = collect();

        $events = $agendaList->map(function ($item) {
            $kegiatanDate = Carbon::parse($item->tanggal_kegiatan);
            $statusAcara = 'mendatang';

            if ($kegiatanDate->isPast() && !Carbon::today()->isSameDay($kegiatanDate)) {
                $statusAcara = 'terlaksana';
            } elseif (Carbon::today()->isSameDay($kegiatanDate)) {
                $statusAcara = 'berlangsung';
            }

            $pegawaiDitugaskan = $item->disposisi
                ->pluck('nip_penerima')
                ->filter()
                ->toArray();
                
            return [
                'id' => $item->id_surat,
                'title' => $item->perihal,
                'start' => $item->tanggal_kegiatan . 'T' . $item->waktu_mulai_kegiatan,
                'end' => $item->tanggal_kegiatan . 'T' . $item->waktu_selesai_kegiatan,
                'extendedProps' => [
                    'lokasi' => $item->lokasi_kegiatan,
                    'status' => $statusAcara,
                    'daftar_staff' => $pegawaiDitugaskan
                ]
            ];
        });

        // 2. Logika Cerdas untuk Card Sidebar (Ringkasan Agenda 3 Terdekat)
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
            'ringkasanAgenda' => $ringkasanAgenda // Lempar ke View
        ]);
    }
}