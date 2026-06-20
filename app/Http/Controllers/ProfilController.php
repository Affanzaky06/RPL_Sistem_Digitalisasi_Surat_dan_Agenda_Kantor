<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Disposisi;
use App\Models\Peserta; // Wajib import Peserta
use App\Models\Surat; // Wajib ditambahkan
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Wajib ditambahkan
use Illuminate\Support\Facades\Hash;

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
       $ringkasanAgenda = \App\Models\Agenda::whereHas('peserta', function($q) use ($user) {
                $q->where('nip', $user->nip);
            })
            ->with(['surat', 'peserta.pegawai']) // Wajib agar tidak null di view
            ->whereDate('tanggal_kegiatan', '>=', \Carbon\Carbon::today())
            ->orderBy('tanggal_kegiatan', 'asc')
            ->orderBy('waktu_mulai', 'asc')
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

        // 1. Jika form yang dikirim adalah Foto Profil
        if ($request->hasFile('foto_profil')) {
            $namaFile = time() . '.' . $request->foto_profil->extension();
            $request->foto_profil->storeAs('profil', $namaFile, 'public');
            $user->foto_profil = $namaFile;
        }

        // 2. Jika form yang dikirim adalah Email
        if ($request->has('email')) {
            $user->email = $request->email;
        }

        // 3. Jika form yang dikirim adalah No Telp
        if ($request->has('no_telp')) {
            $user->no_telp = $request->no_telp;
        }

        // 4. Jika form yang dikirim adalah Password Baru
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui');
    }

   public function konfirmasiPendamping($id_surat, $keputusan)
    {
        $user = Auth::user();
        
        // Find agenda berdasarkan id_surat
        $agenda = Agenda::where('id_surat', $id_surat)->firstOrFail();

        if ($keputusan === 'Hadir') {
            // 1. Ubah status kehadiran di tabel Peserta menjadi 'Hadir'
            Peserta::where('id_agenda', $agenda->id_agenda)
                ->where('nip', $user->nip)
                ->update(['status_kehadiran' => 'Hadir']);

            // 2. Ubah status di tabel Disposisi agar surat hilang dari antrean masuk
            Disposisi::where('id_surat', $id_surat)
                ->where('nip_penerima', $user->nip)
                ->whereIn('status', ['Belum Dibaca', 'Menunggu Konfirmasi'])
                ->update(['status' => 'Hadir']); // Atau bisa gunakan 'Hadir'

            return back()->with('success', 'Berhasil menyetujui pendampingan agenda.');
        } else {
            // 1. Jika menolak, hapus baris user tersebut dari daftar peserta agenda
            Peserta::where('id_agenda', $agenda->id_agenda)
                ->where('nip', $user->nip)
                ->delete();

            // 2. Ubah status di tabel Disposisi menjadi 'Tidak Hadir' atau 'Ditolak'
            Disposisi::where('id_surat', $id_surat)
                ->where('nip_penerima', $user->nip)
                ->update(['status' => 'Tidak Hadir']);

            return back()->with('success', 'Anda menolak undangan pendampingan agenda.');
        }
    }
}
