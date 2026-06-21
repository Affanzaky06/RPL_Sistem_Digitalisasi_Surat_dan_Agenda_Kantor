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
        // Untuk notifikasi list, kita ambil disposisi yang relevan
        if (in_array($user->id_jabatan, ['J005', 'J007'])) {
            $notifList = collect(); // Frontliner/Kepegawaian tidak dapat disposisi
            $totalSuratBaru = Surat::where('status', 'Terverifikasi')->whereDate('tanggal_verifikasi', Carbon::today())->count();
            $totalNotifikasi = Surat::where('status', 'Terverifikasi')->count();
        } else {
            $notifList = \App\Models\Disposisi::with('surat')
                ->where('nip_penerima', $user->nip)
                ->whereIn('status', ['Menunggu Konfirmasi', 'Perwakilan'])
                ->latest('tanggal')
                ->take(5)
                ->get();
            
            $totalSuratBaru = \App\Models\Disposisi::where('nip_penerima', $user->nip)
                ->where('status', 'Menunggu Konfirmasi')
                ->whereDate('tanggal', Carbon::today())
                ->count();
            
            $totalNotifikasi = \App\Models\Disposisi::where('nip_penerima', $user->nip)
                ->whereIn('status', ['Menunggu Konfirmasi', 'Perwakilan'])
                ->count();
        }

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
                $q->whereIn('status_kehadiran', ['Hadir', 'Perwakilan']);
                })
                ->whereDate('tanggal_kegiatan', '>=', Carbon::today())
                ->count();
            
            $ringkasanAgenda = Agenda::whereHas('peserta', function($q) use ($user) {
                    $q->where('nip', $user->nip);
                $q->whereIn('status_kehadiran', ['Hadir', 'Perwakilan']);
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
            'notifikasi' => $notifList,
            'totalSuratBaru' => $totalSuratBaru,
            'totalNotifikasi' => $totalNotifikasi,
            'totalAgenda' => $totalAgenda,
            'ringkasanAgenda' => $ringkasanAgenda
        ]);
    }
}