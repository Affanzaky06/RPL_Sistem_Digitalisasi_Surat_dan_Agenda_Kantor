<?php

namespace App\Http\Controllers\KepalaKantor;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use App\Models\Pegawai;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposisiKepalaController extends Controller
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

        // 3. Menghapus kode yang tidak terpakai ($queryAgenda)

        $ringkasanAgenda = \App\Models\Agenda::whereHas('peserta', function ($q) use ($user) {
            $q->where('nip', $user->nip);
            $q->where('status_kehadiran', 'Hadir');
        })
            ->with(['surat', 'peserta.pegawai']) // Wajib agar tidak null di view
            ->where(function ($query) {
                $query->whereDate('tanggal_kegiatan', '>', \Carbon\Carbon::today())
                    ->orWhere(function ($q) {
                        $q->whereDate('tanggal_kegiatan', '=', \Carbon\Carbon::today())
                            ->whereTime('waktu_selesai', '>', \Carbon\Carbon::now()->format('H:i:s'));
                    });
            })
            ->orderBy('tanggal_kegiatan', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->take(3)
            ->get();


        $search = trim((string) request('search', ''));
        $sort = request('sort', 'prioritas');

        $suratMasukQuery = Surat::with('disposisi')
            ->where('status', 'Terverifikasi')
            ->whereDoesntHave('agenda') // Tambahkan ini! Jika sudah ada agenda, berarti sudah diproses (Kepala Hadir)
            ->whereDoesntHave('disposisi');

        if ($search !== '') {
            $suratMasukQuery->where(function ($q) use ($search) {
                $q->where('asal_surat', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        if ($sort === 'terbaru') {
            $suratMasukQuery->latest('tanggal_verifikasi');
        } elseif ($sort === 'terlama') {
            $suratMasukQuery->oldest('tanggal_verifikasi');
        } else {
            $suratMasukQuery
                ->orderByRaw("CASE prioritas WHEN 'Tinggi' THEN 1 WHEN 'Sedang' THEN 2 WHEN 'Rendah' THEN 3 ELSE 4 END")
                ->latest('tanggal_verifikasi');
        }

        $suratMasuk = $suratMasukQuery->paginate(10)->withQueryString();

        $pegawai = Pegawai::with('bidang')
            ->whereIn('id_jabatan', [
                'J002',
                'J006'
            ])
            ->get();

        return view(
            'disposisi.disposisiKepala',
            [
                'title' => $role,
                'role' => $role,
                'suratMasuk' => $suratMasuk,
                'ringkasanAgenda' => $ringkasanAgenda,

                'pegawai'
                => $pegawai
            ]
        );
    }

    public function disposisi(Request $request, $id)
    {
        $request->validate([
            'nip_penerima' => 'required|exists:pegawai,nip',
            'catatan' => 'nullable|string|max:500'
        ]);

        $suratValid = Surat::where('id_surat', $id)
            ->whereIn('status', ['Terverifikasi', 'Didisposisikan'])
            ->first();

        if (!$suratValid) {
            return back()->with('error', 'Akses ditolak: Surat belum diverifikasi atau tidak valid.');
        }

        $suratValid->update([
            'status' => 'Didisposisikan'
        ]);

        $disposisiEksis = Disposisi::where('id_surat', $id)
            ->where('nip_penerima', $request->nip_penerima)
            ->latest('id_disposisi')
            ->first();

        if ($disposisiEksis) {
            if ($disposisiEksis->status === 'Tidak Hadir') {
                $disposisiEksis->update([
                    'tanggal' => now(),
                    'catatan' => $request->catatan ?? '-',
                    'status' => 'Menunggu Konfirmasi'
                ]);

                return back()->with(
                    'success',
                    'Disposisi berhasil dikirim ulang'
                );
            } else {
                return back()->with(
                    'error',
                    'Surat sudah didisposisikan ke pegawai tersebut.'
                );
            }
        }

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

    public function konfirmasiHadir(Request $request, $id_surat)
    {
        $request->validate([
            'nip_pendamping' => 'nullable|array',
            'nip_pendamping.*' => 'exists:pegawai,nip',
            'catatan' => 'nullable|string|max:500'
        ]);

        $surat = Surat::findOrFail($id_surat);
        $user = Auth::user();

        // Cek Bentrok Jadwal
        if ($surat->tanggal_kegiatan && $surat->waktu_mulai_kegiatan && $surat->waktu_selesai_kegiatan) {
            $bentrok = \App\Models\Agenda::checkConflict(
                $user->nip,
                $surat->tanggal_kegiatan,
                $surat->waktu_mulai_kegiatan,
                $surat->waktu_selesai_kegiatan
            );

            if ($bentrok) {
                return back()->with('error', 'Tidak bisa menghadiri. Jadwal bertabrakan dengan acara: ' . $bentrok->nama_kegiatan . ' (' . \Carbon\Carbon::parse($bentrok->waktu_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($bentrok->waktu_selesai)->format('H:i') . '). Silakan disposisikan surat ini atau batalkan kehadiran acara sebelumnya jika acara ini lebih penting.');
            }
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. Buat Agenda jika belum ada
            $agenda = \App\Models\Agenda::firstOrCreate(
                ['id_surat' => $surat->id_surat],
                [
                    'nama_kegiatan' => $surat->perihal,
                    'tanggal_kegiatan' => $surat->tanggal_kegiatan,
                    'lokasi' => $surat->lokasi_kegiatan,
                    'waktu_mulai' => $surat->waktu_mulai_kegiatan,
                    'waktu_selesai' => $surat->waktu_selesai_kegiatan,
                ]
            );

            // 2. Masukkan Kepala sebagai peserta wajib
            \App\Models\Peserta::updateOrCreate(
                [
                    'id_agenda' => $agenda->id_agenda,
                    'nip' => $user->nip
                ],
                [
                    'status_kehadiran' => 'Hadir'
                ]
            );

            // 3. JIKA ADA PENDAMPING YANG DICEKLIS (Bisa lebih dari 1 orang)
            if ($request->has('nip_pendamping') && is_array($request->nip_pendamping)) {

                // Lakukan perulangan untuk setiap NIP yang diceklis di UI
                foreach ($request->nip_pendamping as $nipPenerima) {

                    // Buat Disposisi untuk masing-masing pendamping
                    $disposisi = \App\Models\Disposisi::create([
                        'id_surat' => $surat->id_surat,
                        'nip_pemberi' => $user->nip,
                        'nip_penerima' => $nipPenerima,
                        'tanggal' => now(),
                        'catatan' => $request->catatan ?? 'Mendampingi Kepala Kantor pada kegiatan ini.',
                        'status' => 'Menunggu Konfirmasi'
                    ]);

                    // Masukkan mereka ke tabel Peserta (Menunggu Konfirmasi)
                    \App\Models\Peserta::updateOrCreate(
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

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Berhasil mengonfirmasi kehadiran dan mengundang pendamping.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses data: ' . $e->getMessage());
        }
    }

    public function tolak(Request $request, $id_surat)
    {
        $surat = Surat::findOrFail($id_surat);

        // 1. Ubah status surat
        $surat->update([
            'status' => 'Ditolak Kepala'
            // 'alasan_tolak' => $request->alasan_tolak // <-- Hapus tanda // jika kolom alasan_tolak sudah Anda tambahkan di database
        ]);

        // 2. Jika sebelumnya pernah di-acc dan masuk agenda, hapus dari agenda
        $agenda = \App\Models\Agenda::where('id_surat', $id_surat)->first();
        if ($agenda) {
            $agenda->peserta()->delete();
            $agenda->delete();
        }

        return back()->with('success', 'Surat berhasil ditolak dan dikembalikan ke Sekretaris.');
    }

    public function batalDisposisi($id)
    {
        $disposisi = Disposisi::findOrFail($id);

        if ($disposisi->nip_pemberi !== Auth::user()->nip) {
            return back()->with('error', 'Akses ditolak: Anda bukan pemberi disposisi ini.');
        }

        $disposisi->delete();

        Surat::where(
            'id_surat',
            $disposisi->id_surat
        )->update([
            'status' => 'Terverifikasi'
        ]);

        return back()->with(
            'success',
            'Disposisi berhasil dibatalkan'
        );
    }
}
