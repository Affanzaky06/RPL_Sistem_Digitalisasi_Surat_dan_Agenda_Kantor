<?php

namespace App\Http\Controllers\KepalaKantor;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardKepalaController extends Controller
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

        // 3. Siapkan Query Dasar (Belum dieksekusi)
        $queryNotifikasi = Surat::query()->where('status', 'Terverifikasi');
        $queryAgenda = Surat::query()->whereNotNull('tanggal_kegiatan');

        // 4. FILTER SAKTI: Jika BUKAN Kepala (J001), tampilkan hanya agenda miliknya!
        if ($user->id_jabatan !== 'J001') {

            // Asumsi: Kita memfilter berdasarkan 'nama' pegawai yang ada di tabel users
            // yang dicocokkan dengan kolom 'tujuan_disposisi' di tabel disposisi.
            $identitasPenerima = $user->nama;

            $queryNotifikasi->whereHas('disposisi', function ($q) use ($identitasPenerima) {
                $q->where('nip_penerima', $identitasPenerima);
            });


            // $queryAgenda->whereHas('disposisi', function ($q) use ($identitasPenerima) {
            //     $q->where('nip_penerima', $identitasPenerima);
            // });
        }

        // 5. Eksekusi semua data menggunakan Clone
        $notifikasi = (clone $queryNotifikasi)->latest('tanggal_verifikasi')->take(5)->get();
        $totalSuratBaru = (clone $queryNotifikasi)->whereDate('tanggal_verifikasi', today())->count();
        $totalNotifikasi = $queryNotifikasi->count();
        // $totalAgenda = (clone $queryAgenda)->whereDate('tanggal_kegiatan', '>=', today())->count();
        // $ringkasanAgenda = (clone $queryAgenda)->orderBy('tanggal_kegiatan', 'asc')->take(3)->get();
        $ringkasanAgenda = collect();
        $totalAgenda = 0;

        // Lempar ke satu view yang sama!
        return view('dashboardKepala', [ // Boleh di-rename jadi 'dashboardUmum' atau 'dashboard'
            'title' =>  $role,
            'role' => $role,
            'notifikasi' => $notifikasi,
            'totalSuratBaru' => $totalSuratBaru,
            'totalNotifikasi' => $totalNotifikasi,
            'totalAgenda' => $totalAgenda,
            'ringkasanAgenda' => $ringkasanAgenda
        ]);
    }
}
