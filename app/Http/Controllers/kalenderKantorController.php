<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KalenderKantorController extends Controller
{
    public function index()
    {
        // 1. Ambil data user yang sedang aktif (login)
        $user = Auth::user();

        // 2. Petakan id_jabatan menjadi nama Role untuk keperluan View/Layout
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

        // 1. Tarik data surat SEKALIGUS data disposisinya (Eager Loading)
        $agendaList = Surat::with('disposisi')
            ->whereNotNull('tanggal_kegiatan')
            ->where('status', 'Terverifikasi')
            ->get();

        // 2. Kumpulkan semua nama pegawai dari tabel disposisi tanpa duplikat
        $daftarStaff = collect();
        // $agendaList->map(function ($surat) {
        //     return $surat->disposisi?->tujuan_disposisi;
        // })->filter()->unique()->values();

        // 3. Format data ke bentuk JSON untuk FullCalendar
        $events = $agendaList->map(function ($item) {
            $kegiatanDate = Carbon::parse($item->tanggal_kegiatan);
            $statusAcara = 'mendatang';

            if ($kegiatanDate->isPast() && !Carbon::today()->isSameDay($kegiatanDate)) {
                $statusAcara = 'terlaksana';
            } elseif (Carbon::today()->isSameDay($kegiatanDate)) {
                $statusAcara = 'berlangsung';
            }

            // Kumpulkan array berisi nama-nama pegawai yang ditugaskan di acara ini
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

                    // 4. Masukkan array pegawai ke properti kalender
                    'daftar_staff' => $pegawaiDitugaskan
                ]
            ];
        });

        return view('KalenderKantor', [
            'title' => $role,
            'role' => $role,
            'events' => $events,
            'daftarStaff' => $daftarStaff
        ]);
    }
}