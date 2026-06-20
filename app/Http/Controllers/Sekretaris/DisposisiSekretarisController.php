<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposisiSekretarisController extends Controller
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
        $ringkasanAgenda = \App\Models\Agenda::whereHas('peserta', function($q) use ($user) {
                $q->where('nip', $user->nip);
            })
            ->with(['surat', 'peserta.pegawai']) // Wajib agar tidak null di view
            ->whereDate('tanggal_kegiatan', '>=', \Carbon\Carbon::today())
            ->orderBy('tanggal_kegiatan', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->take(3)
            ->get();
        $suratMasuk = Surat::with([
            'disposisi.pemberi.bidang'
        ])
            ->whereHas('disposisi', function ($q) {

                $q->where(
                    'nip_penerima',
                    Auth::user()->nip
                )
                    ->whereIn('status', [
                        'Menunggu Konfirmasi',
                        'Belum Dibaca',
                        'Dalam Proses'
                    ]);
            })
            ->latest()
            ->paginate(10);

        return view(
            'disposisi.disposisiSekretaris',
            [
                'title' => $role,
                'role' => $role,
                'suratMasuk' => $suratMasuk,
                'ringkasanAgenda' => $ringkasanAgenda
            ]
        );
    }

    public function konfirmasi_hadir($id_surat)
    {
        Disposisi::where(
            'id_surat',
            $id_surat
        )
            ->where(
                'nip_penerima',
                Auth::user()->nip
            )
            ->update([
                'status' => 'Hadir'
            ]);

        return back()->with(
            'success',
            'Kehadiran berhasil dikonfirmasi'
        );
    }

    public function tolakDispo($id_surat)
    {
        $disposisi = Disposisi::where(
            'id_surat',
            $id_surat
        )
            ->where(
                'nip_penerima',
                Auth::user()->nip
            )
            ->latest('id_disposisi')
            ->firstOrFail();

        $disposisi->update([
            'status' => 'Tidak Hadir'
        ]);

        return back()->with(
            'success',
            'Disposisi ditolak dan dikembalikan ke Kepala'
        );
    }
}
