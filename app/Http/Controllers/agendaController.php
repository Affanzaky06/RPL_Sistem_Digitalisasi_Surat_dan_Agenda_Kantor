<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\Agenda;
use App\Models\Peserta;
use App\Models\Disposisi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            ->whereIn('peserta.status_kehadiran', ['Hadir', 'Perwakilan'])
            ->select('agenda.*', 'surat.perihal', 'surat.nomor_surat', 'surat.asal_surat', 
                     'surat.tanggal_surat', 'surat.prioritas', 'surat.id_surat');

        // --- A. Eksekusi Data untuk FullCalendar ---
        $queryKalender = clone $baseQuery; 
        $agendaKalender = $queryKalender->distinct()->get();

        $events = $agendaKalender->map(function ($item) {
            $now = Carbon::now();
            $mulai = Carbon::parse($item->tanggal_kegiatan . ' ' . $item->waktu_mulai);
            $selesai = Carbon::parse($item->tanggal_kegiatan . ' ' . $item->waktu_selesai);
            
            if ($now->gt($selesai)) {
                $statusAcara = 'terlaksana';
            } elseif ($now->gte($mulai) && $now->lte($selesai)) {
                $statusAcara = 'berlangsung';
            } else {
                $statusAcara = 'mendatang';
            }

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

        $pegawai = \App\Models\Pegawai::with('bidang')
            ->whereIn('id_jabatan', ['J002', 'J006'])
            ->get();

        return view('agenda', [
            'title' => $title,
            'role' => $role, 
            'events' => $events,
            'ringkasanAgenda' => $ringkasanAgenda,
            'pegawai' => $pegawai
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

        $alasan = null;
        // Kepala (J001) tidak perlu alasan
        if ($user->id_jabatan !== 'J001') {
            $request->validate([
                'alasan_tidak_hadir' => 'required|string|max:500',
            ]);
            $alasan = $request->alasan_tidak_hadir;
        }

        $peserta->update([
            'status_kehadiran' => 'Tidak Hadir',
        ]);

        // Teruskan info "Tidak Hadir" ini ke atasan melalui tabel disposisi 
        // Cari disposisi yang mengundang user ke agenda ini
        $agenda = Agenda::findOrFail($id_agenda);
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

        return back()->with('success', 'Status kehadiran berhasil diubah menjadi Tidak Hadir.');
    }

    /**
     * Cek apakah ada pendamping saat Kepala batal hadir
     * Return JSON untuk AJAX
     */
    public function cekPendamping($id_agenda)
    {
        $user = Auth::user();
        $agenda = Agenda::with('surat')->findOrFail($id_agenda);
        $surat = $agenda->surat;

        // Cari pendamping yang DIAJAK OLEH user saat ini di agenda ini
        // Cek melalui tabel Disposisi: nip_pemberi = $user->nip, id_surat = $agenda->id_surat
        $disposisiBawahan = \App\Models\Disposisi::where('id_surat', $agenda->id_surat)
            ->where('nip_pemberi', $user->nip)
            ->pluck('nip_penerima');

        $pendamping = Peserta::where('id_agenda', $id_agenda)
            ->whereIn('nip', $disposisiBawahan)
            ->whereIn('status_kehadiran', ['Hadir', 'Menunggu Konfirmasi'])
            ->with('pegawai.jabatan', 'pegawai.bidang')
            ->get();

        // Cek apakah user saat ini murni HANYA SEBAGAI PENDAMPING
        // Syarat: Ada disposisi ke user ini dari atasan, dan atasan tersebut statusnya 'Hadir' di agenda ini.
        $isPendampingOnly = false;
        if ($user->id_jabatan !== 'J001') {
            $disposisiUser = \App\Models\Disposisi::where('id_surat', $agenda->id_surat)
                ->where('nip_penerima', $user->nip)
                ->latest('id_disposisi')
                ->first();

            if ($disposisiUser && $disposisiUser->nip_pemberi) {
                $atasanHadir = Peserta::where('id_agenda', $id_agenda)
                    ->where('nip', $disposisiUser->nip_pemberi)
                    ->where('status_kehadiran', 'Hadir')
                    ->exists();
                
                if ($atasanHadir) {
                    $isPendampingOnly = true;
                }
            }
        }

        \Log::info("CEK PENDAMPING DEBUG:", [
            'user' => $user->nip,
            'id_agenda' => $id_agenda,
            'disposisiUser' => $disposisiUser ? $disposisiUser->toArray() : null,
            'atasanHadir' => $atasanHadir ?? false,
            'isPendampingOnly' => $isPendampingOnly
        ]);

        // Ambil daftar bawahan untuk disposisi baru berdasarkan jabatan
        $bawahanQuery = \App\Models\Pegawai::with('bidang');

        if ($user->id_jabatan === 'J001') {
            // Kepala: Kabid + Sekretaris
            $bawahanQuery->whereIn('id_jabatan', ['J002', 'J006']);
        } elseif ($user->id_jabatan === 'J002') {
            // Kabid: Subkoor + Staff di bidangnya
            $bawahanQuery->where('id_bidang', $user->id_bidang)
                         ->whereIn('id_jabatan', ['J003', 'J004']);
        } elseif ($user->id_jabatan === 'J003') {
            // Subkoor: Staff di bidangnya
            $bawahanQuery->where('id_bidang', $user->id_bidang)
                         ->where('id_jabatan', 'J004');
        } else {
            // Default kosong jika tidak berhak punya bawahan
            $bawahanQuery->where('nip', 'invalid');
        }

        $bawahan = $bawahanQuery->get();

        return response()->json([
            'is_pendamping_only' => $isPendampingOnly,
            'ada_pendamping' => $pendamping->count() > 0,
            'pendamping' => $pendamping->map(function ($p) {
                return [
                    'nip' => $p->nip,
                    'nama' => $p->pegawai->nama ?? $p->nip,
                    'jabatan' => $p->pegawai->jabatan->nama_jabatan ?? '-',
                    'bidang' => $p->pegawai->bidang->nama_bidang ?? '-',
                    'status' => $p->status_kehadiran,
                ];
            }),
            'bawahan' => $bawahan->map(function ($b) {
                return [
                    'nip' => $b->nip,
                    'nama' => $b->nama,
                    'jabatan' => $b->jabatan->nama_jabatan ?? '-',
                    'bidang' => $b->bidang->nama_bidang ?? '-',
                ];
            }),
            'surat' => $surat ? [
                'asal_surat' => $surat->asal_surat,
                'nomor_surat' => $surat->nomor_surat,
                'perihal' => $surat->perihal,
                'tanggal_surat' => \Carbon\Carbon::parse($surat->tanggal_surat)->format('d-m-Y'),
                'jenis_surat' => $surat->jenis_surat,
                'prioritas' => $surat->prioritas,
            ] : null,
        ]);
    }

    public function wakilkan(Request $request, $id_agenda)
    {
        $user = Auth::user();
        $agenda = Agenda::findOrFail($id_agenda);

        $request->validate([
            'nip_perwakilan' => 'required|string',
        ]);

        $disposisi = Disposisi::where('id_surat', $agenda->id_surat)
            ->where('nip_penerima', $request->nip_perwakilan)
            ->latest('id_disposisi')
            ->first();

        if ($disposisi && $disposisi->status === 'Menunggu Konfirmasi') {
            // Jika pendamping belum konfirmasi, kita jadikan ini seperti disposisi biasa
            // Update Kepala jadi Tidak Hadir (Hanya untuk log/rekam jejak, meskipun nanti agenda dihapus)
            Peserta::where('id_agenda', $id_agenda)
                ->where('nip', $user->nip)
                ->update(['status_kehadiran' => 'Tidak Hadir']);
                
            // Update catatan pendamping agar dia tahu dia sekarang jadi perwakilan/penerima dispo utama
            $disposisi->update([
                'catatan' => 'Atasan Batal Hadir. Anda diminta untuk hadir mewakili beliau (Disposisi).'
            ]);

            // Hapus agenda & peserta (karena Atasan batal, dan pendamping belum konfirmasi, 
            // biarkan pendamping yang akan membuat agendanya sendiri saat dia klik "Hadir" nanti)
            Peserta::where('id_agenda', $id_agenda)->delete();
            $agenda->delete();

            return back()->with('success', 'Agenda dihapus karena Anda batal hadir. Penugasan dialihkan sebagai disposisi biasa ke pendamping (Menunggu Konfirmasi).');
        }

        // Jika pendamping sudah 'Hadir'
        // 1. Update status Kepala menjadi Tidak Hadir
        Peserta::where('id_agenda', $id_agenda)
            ->where('nip', $user->nip)
            ->update(['status_kehadiran' => 'Tidak Hadir']);

        // 2. Update pendamping menjadi Perwakilan
        Peserta::where('id_agenda', $id_agenda)
            ->where('nip', $request->nip_perwakilan)
            ->update(['status_kehadiran' => 'Perwakilan']);

        // 3. Update disposisi pendamping dengan catatan perubahan peran
        if ($disposisi) {
            $disposisi->update([
                'status' => 'Perwakilan',
                'catatan' => 'Anda ditunjuk sebagai perwakilan Atasan pada kegiatan ini.'
            ]);
        }

        return back()->with('success', 'Kehadiran berhasil diwakilkan ke pendamping. Notifikasi perubahan peran telah dikirim.');
    }

    /**
     * Disposisi surat ke bawahan baru saat Kepala batal hadir (tanpa pendamping)
     */
    public function disposisiDariBatalHadir(Request $request, $id_agenda)
    {
        $user = Auth::user();
        $agenda = Agenda::findOrFail($id_agenda);

        $request->validate([
            'nip_penerima' => 'required|string',
        ]);

        // 1. Update status Kepala menjadi Tidak Hadir
        Peserta::where('id_agenda', $id_agenda)
            ->where('nip', $user->nip)
            ->update(['status_kehadiran' => 'Tidak Hadir']);

        // 2. Hapus agenda & peserta yang sudah dibuat supaya tidak terdobel
        Peserta::where('id_agenda', $id_agenda)->delete();
        $agenda->delete();

        // 3. Buat disposisi baru ke bawahan yang dipilih
        Disposisi::create([
            'id_surat' => $agenda->id_surat,
            'nip_pemberi' => $user->nip,
            'nip_penerima' => $request->nip_penerima,
            'tanggal' => now(),
            'catatan' => $request->catatan ?? 'Disposisi dari Kepala Kantor (batal hadir).',
            'status' => 'Menunggu Konfirmasi'
        ]);

        return back()->with('success', 'Agenda dihapus dan surat berhasil didisposisikan ke bawahan.');
    }
}