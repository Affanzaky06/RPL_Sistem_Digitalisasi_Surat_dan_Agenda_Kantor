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

        $queryAgenda = Surat::query()->whereNotNull('tanggal_kegiatan');

        // 4. FILTER SAKTI: Jika BUKAN Kepala (J001), tampilkan hanya agenda miliknya!
        if ($user->id_jabatan !== 'J001') {
            $identitasPenerima = $user->nama;

            $queryAgenda->whereHas('disposisi', function ($q) use ($identitasPenerima) {
                $q->where('nip_penerima', $identitasPenerima);
            });
        }

        $ringkasanAgenda = \App\Models\Agenda::whereHas('peserta', function($q) use ($user) {
                $q->where('nip', $user->nip);
                $q->where('status_kehadiran', 'Hadir');
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
            ->where(function ($query) {

                // surat yang memang diterima Kabid
                $query->whereHas('disposisi', function ($q) {

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

                    // surat yang ditolak bawahan Kabid
                    ->orWhereHas('disposisi', function ($q) {

                        $q->where(
                            'nip_pemberi',
                            Auth::user()->nip
                        )
                            ->where(
                                'status',
                                'Tidak Hadir'
                            );
                    });
            })
            ->latest()
            ->paginate(10);

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

                'pegawai'=> $pegawai
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

    public function konfirmasiHadir(Request $request, $id_surat)
    {
        $surat = Surat::findOrFail($id_surat);
        $user = Auth::user(); // NIP Kabid

        // Update status disposisi Kabid menjadi Hadir
        Disposisi::where('id_surat', $id_surat)
            ->where('nip_penerima', $user->nip)
            ->update(['status' => 'Hadir']);

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

        return back()->with('success', 'Berhasil mengonfirmasi kehadiran dan mengundang pendamping.');
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
