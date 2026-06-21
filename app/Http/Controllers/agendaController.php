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
                ->where('peserta.status_kehadiran', 'Hadir')
            ->where('peserta.status_kehadiran', 'Hadir')
            ->select('agenda.*', 'surat.perihal', 'surat.nomor_surat', 'surat.asal_surat', 
                     'surat.tanggal_surat', 'surat.prioritas', 'surat.id_surat');

        // --- A. Eksekusi Data untuk FullCalendar ---
        $queryKalender = clone $baseQuery; 
        $agendaKalender = $queryKalender->distinct()->get();

        $events = $agendaKalender->map(function ($item) {
            $kegiatanDate = Carbon::parse($item->tanggal_kegiatan);
            $statusAcara = 'mendatang'; 
            
            if ($kegiatanDate->isPast() && !Carbon::today()->isSameDay($kegiatanDate)) {
                $statusAcara = 'terlaksana';
            } elseif (Carbon::today()->isSameDay($kegiatanDate)) {
                $statusAcara = 'berlangsung';
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

        return view('agenda', [
            'title' => $title,
            'role' => $role, 
            'events' => $events,
            'ringkasanAgenda' => $ringkasanAgenda
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
}