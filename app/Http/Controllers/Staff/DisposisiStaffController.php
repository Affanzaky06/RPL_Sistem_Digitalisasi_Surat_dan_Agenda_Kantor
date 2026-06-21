<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Disposisi;
use App\Models\Peserta;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposisiStaffController extends Controller
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

        return view(
            'disposisi.disposisiStaff',
            [
                'title' => $role,
                'role' => $role,
                'suratMasuk' => $suratMasuk,
                'ringkasanAgenda' => collect()
            ]
        );
    }

    public function konfirmasi_hadir($id_surat)
    {
        $surat = Surat::findOrFail($id_surat);
        $user = Auth::user();

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

        $agenda = Agenda::firstOrCreate(
            ['id_surat' => $surat->id_surat],
            [
                'nama_kegiatan' => $surat->perihal,
                'tanggal_kegiatan' => $surat->tanggal_kegiatan,
                'lokasi' => $surat->lokasi_kegiatan,
                'waktu_mulai' => $surat->waktu_mulai_kegiatan,
                'waktu_selesai' => $surat->waktu_selesai_kegiatan,
            ]
        );

        Peserta::updateOrCreate(
            [
                'id_agenda' => $agenda->id_agenda,
                'nip' => $user->nip
            ],
            [
                'status_kehadiran' => 'Hadir'
            ]
        );

        return back()->with(
            'success',
            'Kehadiran berhasil dikonfirmasi'
        );
    }

    public function tolakDispo($id_surat)
    {
        request()->validate([
            'alasan_tolak' => 'required|string|max:500',
        ]);

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
            'status' => 'Tidak Hadir',
            'catatan' => 'Alasan Tidak Hadir: ' . request('alasan_tolak')
        ]);

        $agenda = Agenda::where('id_surat', $id_surat)->first();
        if ($agenda) {
            Peserta::where('id_agenda', $agenda->id_agenda)
                ->where('nip', Auth::user()->nip)
                ->update(['status_kehadiran' => 'Tidak Hadir']);
        }

        $disposisiMasuk = Disposisi::where(
            'id_surat',
            $id_surat
        )
            ->where(
                'nip_penerima',
                $disposisi->nip_pemberi
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
            'Disposisi ditolak dan dikembalikan ke Subkoor'
        );
    }
}
