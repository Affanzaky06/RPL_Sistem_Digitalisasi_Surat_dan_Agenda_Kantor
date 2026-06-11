<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
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
        // 1. DATA UNTUK FULLCALENDAR (Hanya ambil yang diverifikasi)
        $agendaList = Surat::with('disposisi')
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

            return [
                'id' => $item->id_surat,
                'title' => $item->perihal,
                'start' => $item->tanggal_kegiatan . 'T' . $item->waktu_mulai_kegiatan,
                'end' => $item->tanggal_kegiatan . 'T' . $item->waktu_selesai_kegiatan,
                'extendedProps' => [
                    'lokasi' => $item->lokasi_kegiatan,
                    'status' => $statusAcara
                ]
            ];
        });

        // 2. DATA UNTUK SIDEBAR RINGKASAN AGENDA (Ambil 5 agenda ke depan)
        $ringkasanAgenda = Surat::with('disposisi')
            ->whereNotNull('tanggal_kegiatan')
            ->whereDate('tanggal_kegiatan', '>=', Carbon::today())
            ->where('status', 'Terverifikasi')
            ->orderBy('tanggal_kegiatan', 'asc')
            ->take(5)
            ->get()
            ->map(function($item) {
                // MENGGUNAKAN collect() AGAR ANTI CRASH JIKA DATANYA NULL
                $peserta = collect($item->disposisi)->pluck('tujuan_disposisi')->filter()->implode(', ');
                
                return (object) [
                    'nomor_surat' => $item->nomor_surat,
                    'peserta' => $peserta ?: 'Belum ada peserta ditugaskan'
                ];
            });

        return view('agenda', [
            'title' => $role,
            'role' => $role, // Sesuaikan jika dinamis
            'events' => $events,
            'ringkasanAgenda' => $ringkasanAgenda
        ]);
    }
}