<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\Agenda;
use App\Models\Peserta;
use App\Models\Disposisi;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    public function index()
    {
        // 1. Ambil data user yang sedang aktif (login)
        $user = Auth::user();

        // 2. Petakan id_jabatan menjadi nama Role
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
        $title = $role;

        // 3. LOGIKA QUERY MURNI PRIBADI: Tarik agenda HANYA JIKA NIP login terdaftar sebagai peserta
        // Tidak peduli apa jabatannya, aturannya pukul rata untuk semua orang.
        $baseQuery = Peserta::join('agenda', 'peserta.id_agenda', '=', 'agenda.id_agenda')
            ->join('surat', 'agenda.id_surat', '=', 'surat.id_surat')
            ->where('peserta.nip', $user->nip)
                ->where('peserta.status_kehadiran', 'Hadir')
            ->where('peserta.status_kehadiran', 'Hadir')
            ->select('agenda.*', 'surat.perihal', 'surat.nomor_surat', 'surat.asal_surat', 
                     'surat.tanggal_surat', 'surat.prioritas', 'surat.id_surat');

        // --- A. Eksekusi Data untuk FullCalendar ---
        $queryKalender = clone $baseQuery; 
        $agendaKalender = $queryKalender->distinct()->get();

        $events = $agendaKalender->map(function ($item) {
            $mulaiKegiatan = Carbon::parse($item->tanggal_kegiatan . ' ' . $item->waktu_mulai);
            $selesaiKegiatan = Carbon::parse($item->tanggal_kegiatan . ' ' . $item->waktu_selesai);
            $statusAcara = $this->statusAcara($mulaiKegiatan, $selesaiKegiatan);
            $jumlahPesertaHadir = Peserta::where('id_agenda', $item->id_agenda)
                ->where('status_kehadiran', 'Hadir')
                ->count();
            $pendampingHadir = Peserta::with(['pegawai.jabatan', 'pegawai.bidang'])
                ->where('id_agenda', $item->id_agenda)
                ->where('status_kehadiran', 'Hadir')
                ->where('nip', '!=', Auth::user()->nip)
                ->get()
                ->map(function ($peserta) {
                    return [
                        'nip' => $peserta->nip,
                        'nama' => $peserta->pegawai->nama ?? $peserta->nip,
                        'jabatan' => $peserta->pegawai->jabatan->nama_jabatan ?? $peserta->pegawai->id_jabatan ?? null,
                        'bidang' => $peserta->pegawai->bidang->nama_bidang ?? null,
                    ];
                })
                ->values();

            return [
                'id' => $item->id_agenda,
                'title' => $item->nama_kegiatan ?? $item->perihal,
                'start' => $item->tanggal_kegiatan . 'T' . $item->waktu_mulai,
                'end' => $item->tanggal_kegiatan . 'T' . $item->waktu_selesai,
                'extendedProps' => [
                    'lokasi' => $item->lokasi,
                    'status' => $statusAcara,
                    'id_agenda' => $item->id_agenda,
                    'id_surat' => $item->id_surat,
                    'pengirim' => $item->asal_surat,
                    'nomor_surat' => $item->nomor_surat,
                    'perihal' => $item->perihal,
                    'tanggal_surat' => $item->tanggal_surat ? Carbon::parse($item->tanggal_surat)->format('d-m-Y') : '-',
                    'tanggal_kegiatan' => $item->tanggal_kegiatan ? Carbon::parse($item->tanggal_kegiatan)->format('d-m-Y') : '-',
                    'waktu' => ($item->waktu_mulai ? Carbon::parse($item->waktu_mulai)->format('H:i') : '-') 
                             . ' - ' . 
                             ($item->waktu_selesai ? Carbon::parse($item->waktu_selesai)->format('H:i') : '-'),
                    'prioritas' => $item->prioritas,
                    'jumlah_peserta_hadir' => $jumlahPesertaHadir,
                    'pendamping_hadir' => $pendampingHadir,
                ]
            ];
        });

        // --- B. Eksekusi Data untuk Sidebar Ringkasan (5 Terdekat) ---
        $querySidebar = clone $baseQuery;
        $ringkasanAgenda = $querySidebar
            ->whereDate('agenda.tanggal_kegiatan', '>=', Carbon::today())
            ->orderBy('agenda.tanggal_kegiatan', 'asc')
            ->orderBy('agenda.waktu_mulai', 'asc')
            ->distinct()
            ->take(5)
            ->get();

        $pegawaiDisposisi = collect();
        if ($user->id_jabatan === 'J001') {
            $pegawaiDisposisi = Pegawai::with(['bidang', 'jabatan'])
                ->whereIn('id_jabatan', ['J002', 'J006'])
                ->orderBy('nama')
                ->get();
        } elseif ($user->id_jabatan === 'J002') {
            $pegawaiDisposisi = Pegawai::with(['bidang', 'jabatan'])
                ->where('id_bidang', $user->id_bidang)
                ->whereIn('id_jabatan', ['J003', 'J004'])
                ->orderBy('id_jabatan')
                ->orderBy('nama')
                ->get();
        } elseif ($user->id_jabatan === 'J003') {
            $pegawaiDisposisi = Pegawai::with(['bidang', 'jabatan'])
                ->where('nip_atasan', $user->nip)
                ->where('id_jabatan', 'J004')
                ->orderBy('nama')
                ->get();
        }

        return view('agenda', [
            'title' => $title,
            'role' => $role, 
            'events' => $events,
            'ringkasanAgenda' => $ringkasanAgenda,
            'pegawaiDisposisi' => $pegawaiDisposisi
        ]);
    }

    /**
     * Batal hadir dari agenda (untuk semua role)
     * Kepala: langsung batal tanpa alasan
     * Kabid ke bawah: wajib isi alasan dan lempar ke disposisi agar atasan bisa Dispo Ulang
     */
    public function batalHadir(Request $request, $id_agenda)
    {
        $user = Auth::user();

        // Cari record peserta untuk user ini di agenda ini
        $peserta = Peserta::where('id_agenda', $id_agenda)
            ->where('nip', $user->nip)
            ->firstOrFail();

        $agenda = Agenda::findOrFail($id_agenda);
        $jumlahPesertaHadir = Peserta::where('id_agenda', $id_agenda)
            ->where('status_kehadiran', 'Hadir')
            ->count();
        $pendampingHadir = Peserta::where('id_agenda', $id_agenda)
            ->where('status_kehadiran', 'Hadir')
            ->where('nip', '!=', $user->nip)
            ->pluck('nip');
        $kepalaPunyaPendamping = $user->id_jabatan === 'J001' && $pendampingHadir->isNotEmpty();
        $perluDisposisiPengganti = !$kepalaPunyaPendamping
            && $jumlahPesertaHadir <= 1
            && in_array($user->id_jabatan, ['J001', 'J002', 'J003']);

        if ($perluDisposisiPengganti) {
            $request->validate([
                'nip_pengganti' => 'required|exists:pegawai,nip',
            ]);

            $penerimaPengganti = Pegawai::where('nip', $request->nip_pengganti)
                ->when($user->id_jabatan === 'J001', function ($query) {
                    $query->whereIn('id_jabatan', ['J002', 'J006']);
                })
                ->when($user->id_jabatan === 'J002', function ($query) use ($user) {
                    $query->where('id_bidang', $user->id_bidang)
                        ->whereIn('id_jabatan', ['J003', 'J004']);
                })
                ->when($user->id_jabatan === 'J003', function ($query) use ($user) {
                    $query->where('nip_atasan', $user->nip)
                        ->where('id_jabatan', 'J004');
                })
                ->first();

            if (!$penerimaPengganti) {
                return back()->withErrors([
                    'nip_pengganti' => 'Penerima disposisi tidak sesuai dengan kewenangan Anda.'
                ]);
            }
        }

        $alasan = null;
        // Kepala (J001) tidak perlu alasan
        if ($user->id_jabatan !== 'J001') {
            $request->validate([
                'alasan_tidak_hadir' => 'required|string|max:500',
            ]);
            $alasan = $request->alasan_tidak_hadir;
        }

        DB::transaction(function () use ($request, $user, $peserta, $agenda, $alasan, $perluDisposisiPengganti, $kepalaPunyaPendamping, $pendampingHadir) {
            $peserta->update([
                'status_kehadiran' => 'Tidak Hadir',
            ]);

            // Teruskan info "Tidak Hadir" ini ke atasan melalui tabel disposisi
            // Cari disposisi yang mengundang user ke agenda ini
            $disposisi = Disposisi::where('id_surat', $agenda->id_surat)
                ->where('nip_penerima', $user->nip)
                ->latest('id_disposisi')
                ->first();

            if ($disposisi) {
                $disposisi->status = 'Tidak Hadir';
                if ($alasan) {
                    $disposisi->catatan = "Alasan Tidak Hadir: " . $alasan;
                }
                $disposisi->save();
            }

            if ($kepalaPunyaPendamping) {
                foreach ($pendampingHadir as $nipPendamping) {
                    Disposisi::create([
                        'id_surat' => $agenda->id_surat,
                        'nip_pemberi' => $user->nip,
                        'nip_penerima' => $nipPendamping,
                        'tanggal' => now(),
                        'catatan' => 'Menggantikan Kepala Kantor pada agenda ini.',
                        'status' => 'Menunggu Konfirmasi',
                    ]);
                }
            } elseif ($perluDisposisiPengganti) {
                $catatan = $request->catatan_pengganti
                    ?: 'Menggantikan kehadiran pada agenda karena peserta terakhir batal hadir.';

                $disposisiPengganti = Disposisi::create([
                    'id_surat' => $agenda->id_surat,
                    'nip_pemberi' => $user->nip,
                    'nip_penerima' => $request->nip_pengganti,
                    'tanggal' => now(),
                    'catatan' => $catatan,
                    'status' => 'Menunggu Konfirmasi',
                ]);

                Peserta::updateOrCreate(
                    [
                        'id_agenda' => $agenda->id_agenda,
                        'nip' => $request->nip_pengganti,
                    ],
                    [
                        'id_disposisi' => $disposisiPengganti->id_disposisi,
                        'status_kehadiran' => 'Menunggu Konfirmasi',
                    ]
                );
            }
        });

        $pesan = $kepalaPunyaPendamping
            ? 'Kehadiran Kepala dibatalkan dan notifikasi pengganti telah dikirim ke pendamping.'
            : ($perluDisposisiPengganti
            ? 'Status kehadiran berhasil diubah dan agenda telah didisposisikan ke pengganti.'
            : 'Status kehadiran berhasil diubah menjadi Tidak Hadir.');

        return back()->with('success', $pesan);
    }

    private function statusAcara(Carbon $mulaiKegiatan, Carbon $selesaiKegiatan): string
    {
        $sekarang = now();

        if ($sekarang->lt($mulaiKegiatan)) {
            return 'mendatang';
        }

        if ($sekarang->lte($selesaiKegiatan)) {
            return 'berlangsung';
        }

        return 'terlaksana';
    }
}
