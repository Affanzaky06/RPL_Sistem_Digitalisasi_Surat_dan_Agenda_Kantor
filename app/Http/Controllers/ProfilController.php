<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Wajib ditambahkan
use App\Models\Surat; // Wajib ditambahkan

class ProfilController extends Controller
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

        // 3. Siapkan query dasar agenda
        $queryAgenda = Surat::query()->whereNotNull('tanggal_kegiatan');

        // 4. FILTER SAKTI: Jika BUKAN Kepala (J001), tampilkan hanya agenda miliknya!
        if ($user->id_jabatan !== 'J001') {
            // PERBAIKAN: Gunakan NIP, bukan nama, agar cocok dengan kolom nip_penerima
            $identitasPenerima = $user->nip; 

            $queryAgenda->whereHas('disposisi', function ($q) use ($identitasPenerima) {
                $q->where('nip_penerima', $identitasPenerima);
            });
        }

        $ringkasanAgenda = (clone $queryAgenda)->orderBy('tanggal_kegiatan', 'asc')->take(3)->get();

        // 5. Kembalikan ke satu view profil universal
        // Ganti 'profil' dengan nama file blade Anda yang memuat komponen profil tersebut
        return view('profil', [
            'title' =>  $role, // Otomatis menjadi "Profil Kabid", "Profil Staff", dll.
            'role' => $role,
            'user' => $user, // Lempar data $user ini agar bisa dicetak di komponen profil (nama, NIP, dll)
            'ringkasanAgenda' => $ringkasanAgenda
        ]);
    }
}