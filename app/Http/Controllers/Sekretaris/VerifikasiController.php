<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\Peserta; // WAJIB DIIMPORT
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // WAJIB DIIMPORT
use Carbon\Carbon; // WAJIB DIIMPORT

class VerifikasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Surat::query()
            ->where('status', 'Menunggu Verifikasi');

        // SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%")
                    ->orWhere('asal_surat', 'like', "%{$search}%");
            });
        }

        // SORT
        if ($request->sort == 'terlama') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $suratMasuk = $query
            ->paginate(10)
            ->withQueryString();

        // LOGIKA AGENDA: Hanya mengambil agenda milik Sekretaris yang sedang login
        $ringkasanAgenda = Peserta::join('agenda', 'peserta.id_agenda', '=', 'agenda.id_agenda')
            ->join('surat', 'agenda.id_surat', '=', 'surat.id_surat')
            ->select(
                'agenda.id_agenda',
                'agenda.nama_kegiatan',
                'agenda.waktu_mulai',
                'surat.nomor_surat',
                'surat.perihal'
            )
            ->where('peserta.nip', Auth::user()->nip) // Filter khusus NIP pengguna yang login
            ->whereDate('agenda.tanggal_kegiatan', Carbon::today())
            ->whereTime('agenda.waktu_mulai', '>=', Carbon::now()->format('H:i:s'))
            ->orderBy('agenda.waktu_mulai', 'asc')
            ->take(3)
            ->get();

        return view(
            'sekretaris.verifikasiSurat',
            [
                'title' => 'Sekretaris',
                'role' => 'Sekretaris',
                'suratMasuk' => $suratMasuk,
                'ringkasanAgenda' => $ringkasanAgenda // Lempar data agenda ke view
            ]
        );
    }

    public function verifikasi(Request $request, $id)
    {
        if (!$request->prioritas) {
            return back()->with(
                'error',
                'Prioritas wajib dipilih.'
            );
        }

        $surat = Surat::findOrFail($id);
        $surat->update([
            'status' => 'Terverifikasi',
            'prioritas' => $request->prioritas,
            'tanggal_verifikasi' => now()
        ]);

        return back()->with(
            'success',
            'Surat berhasil diverifikasi.'
        );
    }

    public function tolak($id)
    {
        $surat = Surat::findOrFail($id);
        $surat->update([
            'status' => 'Ditolak',
            'tanggal_verifikasi' => now()
        ]);

        return back()->with(
            'success',
            'Surat berhasil ditolak.'
        );
    }

    public function riwayat(Request $request)
    {
        $query = Surat::query()
            ->whereIn(
                'status',
                [
                    'Terverifikasi',
                    'Ditolak'
                ]
            );

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%")
                    ->orWhere('asal_surat', 'like', "%{$search}%");
            });
        }

        if ($request->sort == 'terlama') {
            $query->oldest('tanggal_verifikasi');
        } else {
            $query->latest('tanggal_verifikasi');
        }

        $suratMasuk = $query
            ->paginate(10)
            ->withQueryString();

        // Jalankan logika agenda yang sama di halaman riwayat agar sidebar tidak kosong/error
        $ringkasanAgenda = Peserta::join('agenda', 'peserta.id_agenda', '=', 'agenda.id_agenda')
            ->join('surat', 'agenda.id_surat', '=', 'surat.id_surat')
            ->select(
                'agenda.id_agenda',
                'agenda.nama_kegiatan',
                'agenda.waktu_mulai',
                'surat.nomor_surat',
                'surat.perihal'
            )
            ->where('peserta.nip', Auth::user()->nip) // Filter khusus NIP pengguna yang login
            ->whereDate('agenda.tanggal_kegiatan', Carbon::today())
            ->whereTime('agenda.waktu_mulai', '>=', Carbon::now()->format('H:i:s'))
            ->orderBy('agenda.waktu_mulai', 'asc')
            ->take(3)
            ->get();

        return view(
            'sekretaris.riwayatVerifikasi',
            [
                'title' => 'Sekretaris',
                'role' => 'Sekretaris',
                'suratMasuk' => $suratMasuk,
                'ringkasanAgenda' => $ringkasanAgenda // Lempar data agenda ke view riwayat
            ]
        );
    }
}