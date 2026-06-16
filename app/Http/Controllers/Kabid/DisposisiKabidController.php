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

        $ringkasanAgenda = collect();

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

                'pegawai'
                => $pegawai
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
        $user = Auth::user(); // NIP Kepala

        Disposisi::where(
            'id_surat',
            $id_surat
        )
            ->where(
                'nip_penerima',
                $user->nip
            )
            ->update([
                'status' => 'Hadir'
            ]);

        // 1. PASTIKAN AGENDA SUDAH DIBUAT
        // Cari apakah agenda dari surat ini sudah ada, jika belum buatkan
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

        // 2. MASUKKAN KEPALA SEBAGAI PESERTA PASTI HADIR
        \App\Models\Peserta::updateOrCreate(
            [
                'id_agenda' => $agenda->id_agenda,
                'nip' => $user->nip
            ],
            [
                'status_kehadiran' => 'Hadir' // Langsung Hadir karena Kepala sendiri yang klik
            ]
        );

        // 3. JIKA KEPALA MEMILIH PENDAMPING (DISPOSISI OTOMATIS)
        if ($request->filled('nip_pendamping')) {
            // Buat Disposisi untuk Pendamping
            $disposisi = \App\Models\Disposisi::create([
                'id_surat' => $surat->id_surat,
                'nip_pemberi' => $user->nip,
                'nip_penerima' => $request->nip_pendamping,
                'tanggal' => now(),
                'catatan' => $request->catatan ?? 'Mendampingi Kepala Kantor.',
                'status' => 'Belum Dibaca' // Atau sesuaikan dengan status disposisi awal Anda
            ]);

            // Masukkan Pendamping ke tabel Peserta (Menunggu Konfirmasi mereka)
            \App\Models\Peserta::updateOrCreate(
                [
                    'id_agenda' => $agenda->id_agenda,
                    'nip' => $request->nip_pendamping
                ],
                [
                    'id_disposisi' => $disposisi->id_disposisi, // Ikat dengan disposisinya
                    'status_kehadiran' => 'Menunggu Konfirmasi' // Menunggu pendamping klik terima/hadir
                ]
            );
        }

        // 4. Update status surat (misalnya: Sudah Diproses)
        // $surat->update(['status' => 'Sudah Diproses']); // Opsional, buka komentar jika diperlukan

        return back()->with('success', 'Berhasil mengonfirmasi kehadiran dan menyusun agenda.');
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

    public function konfirmasiPendamping($id_surat, $keputusan)
    {
        $user = Auth::user();
        
        // Find agenda berdasarkan id_surat
        $agenda = \App\Models\Agenda::where('id_surat', $id_surat)->firstOrFail();

        if ($keputusan === 'Hadir') {
            // Jika bersedia hadir mendampingi, ubah status kehadiran menjadi 'Hadir'
            \App\Models\Peserta::where('id_agenda', $agenda->id_agenda)
                ->where('nip', $user->nip)
                ->update(['status_kehadiran' => 'Hadir']);

            return back()->with('success', 'Berhasil menyetujui pendampingan agenda Kepala Kantor.');
        } else {
            // Jika menolak, hapus baris user tersebut dari daftar peserta agenda ini
            \App\Models\Peserta::where('id_agenda', $agenda->id_agenda)
                ->where('nip', $user->nip)
                ->delete();

            return back()->with('success', 'Anda menolak undangan pendampingan agenda.');
        }
    }
}
