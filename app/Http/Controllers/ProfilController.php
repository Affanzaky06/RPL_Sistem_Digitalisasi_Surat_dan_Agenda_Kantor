<?php

namespace App\Http\Controllers;

use App\Models\Surat; // Wajib ditambahkan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Wajib ditambahkan
use Illuminate\Support\Facades\Hash;
use App\Models\Peserta; // Wajib import Peserta
use Carbon\Carbon;

class ProfilController extends Controller
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

        // LOGIKA AGENDA PRIBADI YANG SAMA DENGAN KALENDER/DASHBOARD
        $ringkasanAgenda = Peserta::join('agenda', 'peserta.id_agenda', '=', 'agenda.id_agenda')
            ->join('surat', 'agenda.id_surat', '=', 'surat.id_surat')
            ->select(
                'agenda.id_agenda',
                'agenda.nama_kegiatan',
                'agenda.tanggal_kegiatan',
                'agenda.waktu_mulai',
                'surat.nomor_surat',
                'surat.perihal'
            )
            ->where('peserta.nip', $user->nip) // Saring khusus NIP user yang sedang login
            ->whereDate('agenda.tanggal_kegiatan', '>=', Carbon::today()) // Tampilkan agenda mulai hari ini ke depan
            ->orderBy('agenda.tanggal_kegiatan', 'asc')
            ->orderBy('agenda.waktu_mulai', 'asc')
            ->distinct()
            ->take(3)
            ->get();

        return view('profil', [
            'title' =>  $role, 
            'role' => $role,
            'user' => $user, 
            'ringkasanAgenda' => $ringkasanAgenda
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'nullable|email',
            'no_telp' => 'nullable|max:20',
            'password' => 'nullable|min:8|confirmed',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('foto_profil')) {

            $namaFile = time() . '.' .
                $request->foto_profil->extension();

            $request->foto_profil->storeAs(
                'profil',
                $namaFile,
                'public'
            );

            $user->foto_profil = $namaFile;
        }

        $user->email = $request->email;
        $user->no_telp = $request->no_telp;

        if ($request->filled('password')) {

            $user->password = Hash::make(
                $request->password
            );
        }

        $user->save();

        return back()->with(
            'success',
            'Profil berhasil diperbarui'
        );
    }
}
