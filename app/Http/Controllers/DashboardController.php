<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Peserta;
use App\Models\Surat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
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

        // ==========================================
        // 1. LOGIKA UTAMA: NOTIFIKASI SURAT MASUK
        // ==========================================
        if (in_array($user->id_jabatan, ['J005', 'J007'])) {
            // Frontliner & Kepegawaian melihat semua surat terverifikasi kantor
            $queryNotifikasi = Surat::where('status', 'Terverifikasi');
        } else {
            // Role lain hanya melihat jika mereka menerima disposisi surat tersebut
            $queryNotifikasi = Surat::where('status', 'Terverifikasi')
                ->whereHas('disposisi', function ($q) use ($user) {
                    $q->where('nip_penerima', $user->nip);
                });
        }

        $notifikasi = (clone $queryNotifikasi)->latest('tanggal_verifikasi')->take(5)->get();
        $totalSuratBaru = (clone $queryNotifikasi)->whereDate('tanggal_verifikasi', Carbon::today())->count();
        $totalNotifikasi = $queryNotifikasi->count();

        // ==========================================
        // 2. LOGIKA UTAMA: RINGKASAN AGENDA & PESERTA
        // ==========================================
        if (in_array($user->id_jabatan, ['J005', 'J007'])) {
            // Frontliner & Kepegawaian: Ambil 3 Agenda Kantor terdekat mendatang
            $totalAgenda = Agenda::whereDate('tanggal_kegiatan', '>=', Carbon::today())->count();
            
            $ringkasanAgenda = Agenda::with(['surat', 'peserta.pegawai']) // Eager load relasi
                ->whereDate('tanggal_kegiatan', '>=', Carbon::today())
                ->orderBy('tanggal_kegiatan', 'asc')
                ->orderBy('waktu_mulai', 'asc')
                ->take(3)
                ->get();
        } else {
            // Role Lain: Hanya mengambil agenda di mana NIP mereka terdaftar sebagai peserta
            $totalAgenda = Agenda::whereHas('peserta', function($q) use ($user) {
                    $q->where('nip', $user->nip);
                $q->where('status_kehadiran', 'Hadir');
                })
                ->whereDate('tanggal_kegiatan', '>=', Carbon::today())
                ->count();
            
            $ringkasanAgenda = Agenda::whereHas('peserta', function($q) use ($user) {
                    $q->where('nip', $user->nip);
                $q->where('status_kehadiran', 'Hadir');
                })
                ->with(['surat', 'peserta.pegawai']) // Tarik data surat dan info peserta rapat
                ->whereDate('tanggal_kegiatan', '>=', Carbon::today())
                ->orderBy('tanggal_kegiatan', 'asc')
                ->orderBy('waktu_mulai', 'asc')
                ->take(3)
                ->get();
        }

        return view('dashboardKepala', [ 
            'title' => $role,
            'role' => $role,
            'notifikasi' => $notifikasi,
            'totalSuratBaru' => $totalSuratBaru,
            'totalNotifikasi' => $totalNotifikasi,
            'totalAgenda' => $totalAgenda,
            'ringkasanAgenda' => $ringkasanAgenda
        ]);
    }
}