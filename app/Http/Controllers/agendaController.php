<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\Agenda;
use App\Models\Peserta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    public function index()
    {
        // 1. Ambil data user yang sedang aktif (login)
        $user = Auth::user();

        // 2. Petakan id_jabatan menjadi nama Role
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
        $title = $role;

        // 3. LOGIKA QUERY MURNI PRIBADI: Tarik agenda HANYA JIKA NIP login terdaftar sebagai peserta
        // Tidak peduli apa jabatannya, aturannya pukul rata untuk semua orang.
        $baseQuery = Peserta::join('agenda', 'peserta.id_agenda', '=', 'agenda.id_agenda')
            ->join('surat', 'agenda.id_surat', '=', 'surat.id_surat')
            ->where('peserta.nip', $user->nip)
            ->select('agenda.*', 'surat.perihal', 'surat.nomor_surat');

        // --- A. Eksekusi Data untuk FullCalendar ---
        $queryKalender = clone $baseQuery; 
        $agendaKalender = $queryKalender->distinct()->get();

        $events = $agendaKalender->map(function ($item) {
            $kegiatanDate = Carbon::parse($item->tanggal_kegiatan);
            $statusAcara = 'mendatang'; 
            
            if ($kegiatanDate->isPast() && !Carbon::today()->isSameDay($kegiatanDate)) {
                $statusAcara = 'terlaksana';
            } elseif (Carbon::today()->isSameDay($kegiatanDate)) {
                $statusAcara = 'berlangsung';
            }

            return [
                'id' => $item->id_agenda,
                'title' => $item->nama_kegiatan ?? $item->perihal,
                'start' => $item->tanggal_kegiatan . 'T' . $item->waktu_mulai,
                'end' => $item->tanggal_kegiatan . 'T' . $item->waktu_selesai,
                'extendedProps' => [
                    'lokasi' => $item->lokasi,
                    'status' => $statusAcara
                ]
            ];
        });

        // --- B. Eksekusi Data untuk Sidebar Ringkasan (5 Terdekat) ---
        $querySidebar = clone $baseQuery;
        $ringkasanAgenda = $querySidebar
            ->whereDate('agenda.tanggal_kegiatan', '>=', Carbon::today())
            ->orderBy('agenda.tanggal_kegiatan', 'asc')
            ->orderBy('agenda.waktu_mulai', 'asc')
            ->distinct()
            ->take(5)
            ->get();

        return view('agenda', [
            'title' => $title,
            'role' => $role, 
            'events' => $events,
            'ringkasanAgenda' => $ringkasanAgenda
        ]);
    }
}