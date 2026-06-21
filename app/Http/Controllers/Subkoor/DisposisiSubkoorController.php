<?php

namespace App\Http\Controllers\Subkoor;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use App\Models\Pegawai;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposisiSubkoorController extends Controller
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

        $ringkasanAgenda = collect();

        $search = trim((string) request('search', ''));
        $sort = request('sort', 'prioritas');

        $suratMasukQuery = Surat::with([
            'disposisi.pemberi.bidang'
        ])
            ->whereHas('disposisi', function ($q) use ($user) {
                $q->where('nip_penerima', $user->nip)
                    ->whereIn('status', [
                        'Menunggu Konfirmasi',
                        'Belum Dibaca',
                        'Dalam Proses'
                    ]);
            });

        if ($search !== '') {
            $suratMasukQuery->where(function ($q) use ($search) {
                $q->where('asal_surat', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        if ($sort === 'terbaru') {
            $suratMasukQuery->orderByDesc('tanggal_surat');
        } elseif ($sort === 'terlama') {
            $suratMasukQuery->orderBy('tanggal_surat', 'asc');
        } else {
            $suratMasukQuery
                ->orderByRaw("CASE prioritas WHEN 'Tinggi' THEN 1 WHEN 'Sedang' THEN 2 WHEN 'Rendah' THEN 3 ELSE 4 END")
                ->orderByDesc('tanggal_surat');
        }

        $suratMasuk = $suratMasukQuery->paginate(10)->withQueryString();

        $pegawai = Pegawai::with('bidang')
            ->where(
                'id_bidang',
                $user->id_bidang
            )
            ->where(
                'id_jabatan',
                'J004'
            )
            ->get();

        return view(
            'disposisi.disposisiSubkoor',
            [
                'title' => $role,
                'role' => $role,
                'suratMasuk' => $suratMasuk,
                'ringkasanAgenda' => $ringkasanAgenda,
                'pegawai' => $pegawai
            ]
        );
    }

    public function disposisi(Request $request, $id)
    {
        $request->validate([
            'nip_penerima' => 'required',
        ]);

        Disposisi::where(
            'id_surat',
            $id
        )
            ->where(
                'nip_penerima',
                Auth::user()->nip
            )
            ->update([
                'status' => 'Didisposisikan'
            ]);

        Disposisi::create([

            'id_surat' => $id,

            'nip_pemberi' => Auth::user()->nip,

            'nip_penerima' => $request->nip_penerima,

            'tanggal' => now(),

            'catatan' => $request->catatan ?? '-',

            'status' => 'Menunggu Konfirmasi'

        ]);

        return back()->with(
            'success',
            'Disposisi berhasil dikirim'
        );
    }

    public function tolak(Request $request, $id_surat)
    {
        $disposisi = Disposisi::where(
            'id_surat',
            $id_surat
        )
            ->where(
                'nip_penerima',
                Auth::user()->nip
            )
            ->latest()
            ->firstOrFail();

        $disposisi->update([
            'status' => 'Tidak Hadir'
        ]);

        return back()->with(
            'success',
            'Disposisi ditolak dan dikembalikan ke Kabid'
        );
    }

    public function batalDisposisi($id)
    {
        $disposisi = Disposisi::findOrFail($id);

        // disposisi ke Staff dibatalkan
        $disposisi->update([
            'status' => 'Dibatalkan'
        ]);

        // kembalikan disposisi yang diterima Subkoor
        $disposisiMasuk = Disposisi::where(
            'id_surat',
            $disposisi->id_surat
        )
            ->where(
                'nip_penerima',
                Auth::user()->nip
            )
            ->latest('id_disposisi')
            ->first();

        if ($disposisiMasuk) {

            $disposisiMasuk->update([
                'status' => 'Menunggu Konfirmasi'
            ]);
        }

        return back()->with(
            'success',
            'Disposisi berhasil dibatalkan'
        );
    }
}
