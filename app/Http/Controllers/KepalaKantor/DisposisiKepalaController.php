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

         $ringkasanAgenda = \App\Models\Agenda::whereHas('peserta', function($q) use ($user) {
                $q->where('nip', $user->nip);
            })
            ->with(['surat', 'peserta.pegawai']) // Wajib agar tidak null di view
            ->whereDate('tanggal_kegiatan', '>=', \Carbon\Carbon::today())
            ->orderBy('tanggal_kegiatan', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->take(3)
            ->get();

        $suratMasuk = Surat::with('disposisi')
            ->where('status', 'Terverifikasi')
            ->where(function ($q) {

                $q->whereDoesntHave('disposisi')

                    ->orWhereHas('disposisi', function ($sub) {

                        $sub->whereRaw("
                            id_disposisi = (
                                SELECT MAX(d2.id_disposisi)
                                FROM disposisi d2
                                WHERE d2.id_surat = disposisi.id_surat
                            )
                        ")
                            ->where('status', 'Tidak Hadir');
                    });
            })
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

        $disposisiLama = Disposisi::where(
            'id_surat',
            $id
        )
            ->where(
                'nip_penerima',
                $request->nip_penerima
            )
            ->where(
                'status',
                'Tidak Hadir'
            )
            ->first();

        if ($disposisiLama) {

            $disposisiLama->update([
                'tanggal' => now(),
                'catatan' => $request->catatan ?? '-',
                'status' => 'Menunggu Konfirmasi'
            ]);

            return back()->with(
                'success',
                'Disposisi berhasil dikirim ulang'
            );
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
        $surat = Surat::findOrFail($id_surat);
        $user = Auth::user(); 

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
                    'status' => 'Belum Dibaca'
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

        return back()->with('success', 'Berhasil mengonfirmasi kehadiran dan mengundang pendamping.');
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
