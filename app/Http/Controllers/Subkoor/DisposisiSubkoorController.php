<?php

namespace App\Http\Controllers\Subkoor;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use App\Models\Agenda;
use App\Models\Pegawai;
use App\Models\Peserta;
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
            ->where('nip_atasan', $user->nip)
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
            'nip_penerima' => 'required|exists:pegawai,nip',
            'catatan' => 'required|string|max:1000',
        ]);

        $penerima = Pegawai::where('nip', $request->nip_penerima)
            ->where('nip_atasan', Auth::user()->nip)
            ->where('id_jabatan', 'J004')
            ->first();

        if (!$penerima) {
            return back()->withErrors([
                'nip_penerima' => 'Subkoor hanya dapat mendisposisikan ke Staff bawahannya.'
            ]);
        }

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

            'catatan' => $request->catatan,

            'status' => 'Menunggu Konfirmasi'

        ]);

        return back()->with(
            'success',
            'Disposisi berhasil dikirim'
        );
    }

    public function konfirmasiHadir(Request $request, $id_surat)
    {
        $surat = Surat::findOrFail($id_surat);
        $user = Auth::user();

        Disposisi::where('id_surat', $id_surat)
            ->where('nip_penerima', $user->nip)
            ->update(['status' => 'Hadir']);

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

        if ($request->has('nip_pendamping') && is_array($request->nip_pendamping)) {
            foreach ($request->nip_pendamping as $nipPenerima) {
                $pegawai = Pegawai::where('nip', $nipPenerima)
                    ->where('nip_atasan', $user->nip)
                    ->where('id_jabatan', 'J004')
                    ->first();

                if (!$pegawai) {
                    continue;
                }

                $disposisi = Disposisi::create([
                    'id_surat' => $surat->id_surat,
                    'nip_pemberi' => $user->nip,
                    'nip_penerima' => $nipPenerima,
                    'tanggal' => now(),
                    'catatan' => $request->catatan ?: 'Mendampingi atasan pada kegiatan ini.',
                    'status' => 'Menunggu Konfirmasi'
                ]);

                Peserta::updateOrCreate(
                    [
                        'id_agenda' => $agenda->id_agenda,
                        'nip' => $nipPenerima
                    ],
                    [
                        'id_disposisi' => $disposisi->id_disposisi,
                        'status_kehadiran' => 'Menunggu Konfirmasi'
                    ]
                );
            }
        }

        return back()->with('success', 'Berhasil mengonfirmasi kehadiran dan mengundang pendamping.');
    }

    public function tolak(Request $request, $id_surat)
    {
        $request->validate([
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
            ->latest()
            ->firstOrFail();

        $disposisi->update([
            'status' => 'Tidak Hadir',
            'catatan' => 'Alasan Tidak Hadir: ' . $request->alasan_tolak
        ]);

        $agenda = Agenda::where('id_surat', $id_surat)->first();
        if ($agenda) {
            Peserta::where('id_agenda', $agenda->id_agenda)
                ->where('nip', Auth::user()->nip)
                ->update(['status_kehadiran' => 'Tidak Hadir']);
        }

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
