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

        $laporan = Disposisi::with([
            'penerima.bidang',
            'pemberi.bidang'
        ])
            ->where(
                'nip_pemberi',
                $user->nip
            )
            ->latest()
            ->paginate(10);

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

        $routeBatalDisposisi = $routeMap[$user->id_jabatan] ?? null;
        
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
        return view(
            'laporanPemantauan',
            [
                'title' => $role,
                'role' => $role,
                'laporan' => $laporan,
                'ringkasanAgenda' => $ringkasanAgenda,
                'pegawai' => $pegawai,
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

    // FUNGSI UNTUK MEMAAFKAN / SETUJUI PENOLAKAN TANPA DISPO ULANG
    public function setujuiPenolakan($id_disposisi)
    {
        $dispoLama = Disposisi::findOrFail($id_disposisi);
        // Ubah status menjadi dimaklumi, jadi sistem tidak minta dispo ulang lagi
        $dispoLama->update(['status' => 'Dimaklumi']);
        
        return back()->with('success', 'Penolakan bawahan berhasil disetujui (Dimaklumi).');
    }
}
