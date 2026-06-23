<?php

namespace App\Http\Controllers\Kabid;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use App\Models\Pegawai;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposisiKabidController extends Controller
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

        $suratMasukQuery = Surat::with([
            'disposisi.pemberi.bidang',
            'disposisi.penerima'
        ])
            ->whereHas('disposisi', function ($q) {
                $q->where('nip_penerima', Auth::user()->nip)
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
            $suratMasukQuery->orderBy('tanggal_surat', 'desc');
        } elseif ($sort === 'terlama') {
            $suratMasukQuery->orderBy('tanggal_surat', 'asc');
        } else {
            $suratMasukQuery
                ->orderByRaw("CASE prioritas WHEN 'Tinggi' THEN 1 WHEN 'Sedang' THEN 2 WHEN 'Rendah' THEN 3 ELSE 4 END")
                ->orderBy('tanggal_surat', 'desc');
        }

        $suratMasuk = $suratMasukQuery->paginate(10)->withQueryString();

        $pegawai = Pegawai::with('bidang')
            ->where('id_bidang', $user->id_bidang)
            ->whereIn('id_jabatan', [
                'J003',
                'J004'
            ])
            ->get();

        return view(
            'disposisi.disposisiKabid',
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
            'catatan' => 'nullable|string|max:500'
        ]);

        $cekKepemilikan = Disposisi::where('id_surat', $id)
            ->where('nip_penerima', Auth::user()->nip)
            ->first();

        if (!$cekKepemilikan) {
            return back()->with('error', 'Akses ditolak: Anda tidak memiliki wewenang atas surat ini.');
        }

        $cekKepemilikan->update([
            'status' => 'Didisposisikan'
        ]);

        $disposisiEksis = Disposisi::where('id_surat', $id)
            ->where('nip_penerima', $request->nip_penerima)
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
        $user = Auth::user(); // NIP Kabid

        // Cek IDOR: Pastikan Kabid benar-benar menerima disposisi surat ini
        $cekDisposisi = Disposisi::where('id_surat', $id_surat)
            ->where('nip_penerima', $user->nip)
            ->first();

        if (!$cekDisposisi) {
            return back()->with('error', 'Akses ditolak: Anda tidak memiliki wewenang untuk surat ini.');
        }

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

            // Update status disposisi Kabid menjadi Hadir
            $cekDisposisi->update(['status' => 'Hadir']);

            // 1. PASTIKAN AGENDA SUDAH DIBUAT
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

            // 2. MASUKKAN KABID SEBAGAI PESERTA PASTI HADIR
            \App\Models\Peserta::updateOrCreate(
                [
                    'id_agenda' => $agenda->id_agenda,
                    'nip' => $user->nip
                ],
                [
                    'status_kehadiran' => 'Hadir' // Langsung Hadir karena Kabid sendiri yang klik
                ]
            );

            // 3. JIKA KABID MEMILIH PENDAMPING (BISA LEBIH DARI 1 ORANG)
            if ($request->has('nip_pendamping') && is_array($request->nip_pendamping)) {

                // Lakukan perulangan untuk setiap NIP yang diceklis di form
                foreach ($request->nip_pendamping as $nipPenerima) {

                    // Buat Disposisi untuk masing-masing Pendamping
                    $disposisi = \App\Models\Disposisi::create([
                        'id_surat' => $surat->id_surat,
                        'nip_pemberi' => $user->nip,
                        'nip_penerima' => $nipPenerima, // <-- Diambil dari variabel perulangan
                        'tanggal' => now(),
                        'catatan' => $request->catatan ?? 'Mendampingi atasan pada kegiatan ini.',
                        'status' => 'Menunggu Konfirmasi'
                    ]);

                    // Masukkan Pendamping ke tabel Peserta (Menunggu Konfirmasi mereka)
                    \App\Models\Peserta::updateOrCreate(
                        [
                            'id_agenda' => $agenda->id_agenda,
                            'nip' => $nipPenerima // <-- Diambil dari variabel perulangan
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
            'Disposisi ditolak dan dikembalikan ke Kepala Kantor'
        );
    }

    public function batalDisposisi($id)
    {
        $disposisi = Disposisi::findOrFail($id);

        if ($disposisi->nip_pemberi !== Auth::user()->nip) {
            return back()->with('error', 'Akses ditolak: Anda bukan pemberi disposisi ini.');
        }

        // disposisi ke bawahan dibatalkan
        $disposisi->update([
            'status' => 'Dibatalkan'
        ]);

        // cari disposisi yang diterima Kabid
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
