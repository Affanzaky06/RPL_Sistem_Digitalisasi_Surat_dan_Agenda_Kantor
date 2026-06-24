<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\Peserta;
use App\Models\Agenda;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class laporanPemantauanController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $search = trim((string) request('search', ''));
        $sort = request('sort', 'terbaru');

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

        $laporanQuery = Disposisi::with([
            'penerima.bidang',
            'pemberi.bidang',
            'peserta',
            'surat' // Ditambahkan agar query relasi surat lebih efisien (Eager Loading)
        ])
            ->where('nip_pemberi', $user->nip);

        if ($search !== '') {
            $laporanQuery->where(function ($query) use ($search) {
                $query->where('catatan', 'like', "%{$search}%")
                    ->orWhereHas('surat', function ($q) use ($search) {
                        $q->where('perihal', 'like', "%{$search}%")
                            ->orWhere('asal_surat', 'like', "%{$search}%");
                    })
                    ->orWhereHas('penerima', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($sort === 'terlama') {
            $laporanQuery->oldest('tanggal');
        } else {
            $laporanQuery->latest('tanggal');
        }

        $laporan = $laporanQuery->paginate(10)->withQueryString();

        $routeMap = [
            'J001' => 'kepala.disposisi.batal',
            'J002' => 'kabid.disposisi.batal',
            'J003' => 'subkoor.disposisi.batal',
        ];

        if ($user->id_jabatan === 'J001') {
            $pegawai = \App\Models\Pegawai::with('bidang')->whereIn('id_jabatan', ['J002', 'J006'])->get();
        } elseif ($user->id_jabatan === 'J002') {
            $pegawai = \App\Models\Pegawai::with('bidang')->where('id_bidang', $user->id_bidang)->whereIn('id_jabatan', ['J003', 'J004'])->get();
        } elseif ($user->id_jabatan === 'J003') {
            $pegawai = \App\Models\Pegawai::with('bidang')->where('id_bidang', $user->id_bidang)->where('id_jabatan', 'J004')->get();
        } else {
            $pegawai = collect();
        }

        // --- SOLUSI: Ekstrak daftar jabatan tersedia untuk dilempar ke View ---
        $jabatanTersedia = $pegawai->map(function ($p) {
            return match ($p->id_jabatan) {
                'J002' => 'Kabid',
                'J003' => 'Subkoor',
                'J004' => 'Staff',
                'J006' => 'Sekretaris',
                default => null,
            };
        })->filter()->unique();

        $routeBatalDisposisi = $routeMap[$user->id_jabatan] ?? null;

        $ringkasanAgenda = \App\Models\Agenda::whereHas('peserta', function ($q) use ($user) {
            $q->where('nip', $user->nip);
            $q->where('status_kehadiran', 'Hadir');
        })
            ->with(['surat', 'peserta.pegawai'])
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

        return view(
            'laporanPemantauan',
            [
                'title' => $role,
                'role' => $role,
                'laporan' => $laporan,
                'search' => $search,
                'sort' => $sort,
                'ringkasanAgenda' => $ringkasanAgenda,
                'pegawai' => $pegawai,
                'jabatanTersedia' => $jabatanTersedia, // <-- WAJIB DIKIRIMKAN DI SINI
                'routeBatalDisposisi' => $routeBatalDisposisi
            ]
        );
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

    public function dispoUlang(Request $request, $id_disposisi)
    {
        $dispoLama = Disposisi::findOrFail($id_disposisi);

        if ($dispoLama->nip_pemberi !== Auth::user()->nip) {
            return back()->with('error', 'Akses ditolak: Anda bukan pemberi disposisi ini.');
        }

        if ($request->has('nip_pendamping') && is_array($request->nip_pendamping)) {
            foreach ($request->nip_pendamping as $nipBaru) {

                // Buat Disposisi Baru
                $dispoBaru = Disposisi::create([
                    'id_surat' => $dispoLama->id_surat,
                    'nip_pemberi' => Auth::user()->nip,
                    'nip_penerima' => $nipBaru,
                    'tanggal' => now(),
                    'catatan' => $request->catatan ?? $dispoLama->catatan, // Bawa catatan baru atau lama
                    'status' => 'Menunggu Konfirmasi'
                ]);

                // Jika surat ini punya Agenda, ikat orang baru ini ke tabel Peserta
                $agenda = Agenda::where('id_surat', $dispoLama->id_surat)->first();
                if ($agenda) {
                    Peserta::create([
                        'id_agenda' => $agenda->id_agenda,
                        'nip' => $nipBaru,
                        'id_disposisi' => $dispoBaru->id_disposisi,
                        'status_kehadiran' => 'Menunggu Konfirmasi'
                    ]);
                }
            }
        }

        // Matikan disposisi lama agar statusnya berubah jadi 'Digantikan' dan label merahnya hilang
        $dispoLama->update(['status' => 'Digantikan']);

        return back()->with('success', 'Disposisi ulang berhasil dikirimkan ke penerima baru.');
    }

    // FUNGSI UNTUK MENGAMBIL ALIH (HADIR SENDIRI) JIKA BAWAHAN MENOLAK
    public function hadirAmbilAlih(Request $request, $id_disposisi)
    {
        $dispoBawahan = Disposisi::findOrFail($id_disposisi);

        if ($dispoBawahan->nip_pemberi !== Auth::user()->nip) {
            return back()->with('error', 'Akses ditolak: Anda bukan pemberi disposisi ini.');
        }

        $surat = Surat::findOrFail($dispoBawahan->id_surat);
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

        // 2. Masukkan Atasan (User Login) sebagai peserta wajib
        // Pastikan kita tahu id_disposisi milik atasan ini (yaitu disposisi awal dari atasannya dia)
        $dispoAtasan = Disposisi::where('id_surat', $surat->id_surat)
            ->where('nip_penerima', $user->nip)
            ->first();

        // Jika atasan ini adalah Kepala Kantor (tidak punya disposisi), dispoAtasan null
        $idDispoAtasan = $dispoAtasan ? $dispoAtasan->id_disposisi : null;

        \App\Models\Peserta::updateOrCreate(
            [
                'id_agenda' => $agenda->id_agenda,
                'nip' => $user->nip
            ],
            [
                'id_disposisi' => $idDispoAtasan,
                'status_kehadiran' => 'Hadir'
            ]
        );

        // 3. JIKA ADA PENDAMPING YANG DICEKLIS
        if ($request->has('nip_pendamping') && is_array($request->nip_pendamping)) {
            foreach ($request->nip_pendamping as $nipPenerima) {
                $disposisi = \App\Models\Disposisi::create([
                    'id_surat' => $surat->id_surat,
                    'nip_pemberi' => $user->nip,
                    'nip_penerima' => $nipPenerima,
                    'tanggal' => now(),
                    'catatan' => $request->catatan ?? 'Mendampingi pimpinan pada kegiatan ini.',
                    'status' => 'Menunggu Konfirmasi'
                ]);

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

        // 4. Ubah status disposisi bawahan yang menolak menjadi "Digantikan"
        $dispoBawahan->update(['status' => 'Digantikan']);

        // 5. Ubah status disposisi milik Atasan ini sendiri menjadi "Hadir" jika ada
        if ($dispoAtasan) {
            $dispoAtasan->update(['status' => 'Hadir']);
        }

        return back()->with('success', 'Berhasil mengambil alih surat dan menjadwalkan kehadiran Anda.');
    }

    // FUNGSI UNTUK MELEMPAR PENOLAKAN KE ATASAN LAGI (TOLAK KE ATASAN)
    public function tolakKeAtasan(Request $request, $id_disposisi)
    {
        $dispoBawahan = Disposisi::findOrFail($id_disposisi);

        if ($dispoBawahan->nip_pemberi !== Auth::user()->nip) {
            return back()->with('error', 'Akses ditolak: Anda bukan pemberi disposisi ini.');
        }

        $user = Auth::user();

        $request->validate([
            'alasan_tolak' => 'required|string|max:1000'
        ]);

        // 1. Cari Disposisi milik Atasan (user login) untuk surat ini
        $dispoAtasan = Disposisi::where('id_surat', $dispoBawahan->id_surat)
            ->where('nip_penerima', $user->nip)
            ->first();

        if (!$dispoAtasan) {
            return back()->with('error', 'Anda tidak memiliki disposisi asal untuk ditolak ke atasan.');
        }

        // 2. Ubah status disposisi Atasan menjadi "Ditolak" dan tambahkan catatan
        $dispoAtasan->update([
            'status' => 'Ditolak',
            'catatan' => $request->alasan_tolak
        ]);

        // 3. Ubah status disposisi bawahan yang asli menjadi "Dimaklumi" agar clear
        $dispoBawahan->update(['status' => 'Dimaklumi']);

        // 4. Jika ada agenda dan user ini tadinya peserta, hapus / set batal
        $agenda = \App\Models\Agenda::where('id_surat', $dispoBawahan->id_surat)->first();
        if ($agenda) {
            \App\Models\Peserta::where('id_agenda', $agenda->id_agenda)
                ->where('nip', $user->nip)
                ->delete();
        }

        return back()->with('success', 'Penolakan berhasil dilempar kembali ke atasan Anda.');
    }

    // FUNGSI UNTUK MEMAAFKAN / SETUJUI PENOLAKAN TANPA DISPO ULANG (UNTUK PENDAMPING)
    public function setujuiPenolakan($id_disposisi)
    {
        $dispoLama = Disposisi::findOrFail($id_disposisi);

        if ($dispoLama->nip_pemberi !== Auth::user()->nip) {
            return back()->with('error', 'Akses ditolak: Anda bukan pemberi disposisi ini.');
        }

        // Ubah status menjadi dimaklumi, jadi sistem tidak minta dispo ulang lagi
        $dispoLama->update(['status' => 'Dimaklumi']);

        return back()->with('success', 'Penolakan bawahan berhasil disetujui (Dimaklumi). Anda akan hadir tanpa pendamping tersebut.');
    }
}
