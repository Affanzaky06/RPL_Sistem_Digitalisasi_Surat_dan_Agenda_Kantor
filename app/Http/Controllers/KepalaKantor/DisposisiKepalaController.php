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

        $queryAgenda = Surat::query()->whereNotNull('tanggal_kegiatan');

        // 4. FILTER SAKTI: Jika BUKAN Kepala (J001), tampilkan hanya agenda miliknya!
        if ($user->id_jabatan !== 'J001') {
            $identitasPenerima = $user->nama;

            $queryAgenda->whereHas('disposisi', function ($q) use ($identitasPenerima) {
                $q->where('nip_penerima', $identitasPenerima);
            });
        }

        $ringkasanAgenda = collect();

        $suratMasuk = Surat::query()
            ->where('status', 'Terverifikasi')
            ->whereDoesntHave('disposisi')
            ->latest('tanggal_verifikasi')
            ->paginate(10);

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
            'nip_penerima' => 'required',
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
        $surat = Surat::findOrFail($id_surat);

        // 1. Ubah status surat
        $surat->update([
            'status' => 'Ditolak Kepala'
            // 'alasan_tolak' => $request->alasan_tolak // <-- Hapus tanda // jika kolom alasan_tolak sudah Anda tambahkan di database
        ]);

        // 2. Jika sebelumnya pernah di-acc dan masuk agenda, hapus dari agenda
        $agenda = \App\Models\Agenda::where('id_surat', $id_surat)->first();
        if ($agenda) {
            \App\Models\Peserta::where('id_agenda', $agenda->id_agenda)->delete();
            $agenda->delete();
        }

        return back()->with('success', 'Surat berhasil ditolak dan dikembalikan ke Sekretaris.');
    }

    public function batalDisposisi($id)
    {
        $disposisi = Disposisi::findOrFail($id);

        $disposisi->delete();

        return back()->with(
            'success',
            'Disposisi berhasil dibatalkan'
        );
    }
}
