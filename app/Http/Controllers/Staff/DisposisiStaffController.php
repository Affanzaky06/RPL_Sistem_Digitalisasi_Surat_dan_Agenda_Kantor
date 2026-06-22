<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Disposisi;
use App\Models\Pegawai;
use App\Models\Peserta;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposisiStaffController extends Controller
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

        $search = trim((string) request('search', ''));
        $sort = request('sort', 'prioritas');

        $suratMasukQuery = Surat::with([
            'disposisi.pemberi.bidang'
        ])
            ->whereHas('disposisi', function ($q) use ($user) {
                $q->where('nip_penerima', $user->nip)
                    ->whereIn('status', [
                        'Menunggu Konfirmasi',
                        'Belum Dibaca',
                        'Dalam Proses'
                    ]);
            });

        if ($search !== '') {
            $suratMasukQuery->where(function ($q) use ($search) {
                $q->where('asal_surat', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        if ($sort === 'terbaru') {
            $suratMasukQuery->orderByDesc('tanggal_surat');
        } elseif ($sort === 'terlama') {
            $suratMasukQuery->orderBy('tanggal_surat', 'asc');
        } else {
            $suratMasukQuery
                ->orderByRaw("CASE prioritas WHEN 'Tinggi' THEN 1 WHEN 'Sedang' THEN 2 WHEN 'Rendah' THEN 3 ELSE 4 END")
                ->orderByDesc('tanggal_surat');
        }

        $suratMasuk = $suratMasukQuery->paginate(10)->withQueryString();

        $ringkasanAgenda = Agenda::whereHas('peserta', function ($q) use ($user) {
            $q->where('nip', $user->nip);
            $q->whereIn('status_kehadiran', ['Hadir', 'Perwakilan']);
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
            'disposisi.disposisiStaff',
            [
                'title' => $role,
                'role' => $role,
                'suratMasuk' => $suratMasuk,
                'ringkasanAgenda' => $ringkasanAgenda
            ]
        );
    }

    public function konfirmasi_hadir($id_surat)
    {
        $surat = Surat::findOrFail($id_surat);
        $user = Auth::user();

        // Cek Bentrok Jadwal
        if ($surat->tanggal_kegiatan && $surat->waktu_mulai_kegiatan && $surat->waktu_selesai_kegiatan) {
            $bentrok = Agenda::checkConflict(
                $user->nip, 
                $surat->tanggal_kegiatan, 
                $surat->waktu_mulai_kegiatan, 
                $surat->waktu_selesai_kegiatan
            );
            
            if ($bentrok) {
                return back()->with('error', 'Tidak bisa menghadiri. Jadwal bertabrakan dengan acara: ' . $bentrok->nama_kegiatan . ' (' . \Carbon\Carbon::parse($bentrok->waktu_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($bentrok->waktu_selesai)->format('H:i') . '). Silakan tolak surat ini atau batalkan kehadiran acara sebelumnya jika acara ini lebih penting.');
            }
        }

        // Update status disposisi Staff menjadi Hadir
        Disposisi::where('id_surat', $id_surat)
            ->where('nip_penerima', $user->nip)
            ->update(['status' => 'Hadir']);

        // PASTIKAN AGENDA SUDAH DIBUAT
        $agenda = Agenda::firstOrCreate(
            ['id_surat' => $surat->id_surat],
            [
                'nama_kegiatan' => $surat->perihal,
                'tanggal_kegiatan' => $surat->tanggal_kegiatan,
                'lokasi' => $surat->lokasi_kegiatan,
                'waktu_mulai' => $surat->waktu_mulai_kegiatan,
                'waktu_selesai' => $surat->waktu_selesai_kegiatan,
            ]
        );

        // MASUKKAN STAFF SEBAGAI PESERTA PASTI HADIR
        Peserta::updateOrCreate(
            [
                'id_agenda' => $agenda->id_agenda,
                'nip' => $user->nip
            ],
            [
                'status_kehadiran' => 'Hadir'
            ]
        );

        return back()->with(
            'success',
            'Kehadiran berhasil dikonfirmasi'
        );
    }

    public function tolakDispo($id_surat)
    {
        $disposisi = Disposisi::where(
            'id_surat',
            $id_surat
        )
            ->where(
                'nip_penerima',
                Auth::user()->nip
            )
            ->latest('id_disposisi')
            ->firstOrFail();

        $disposisi->update([
            'status' => 'Tidak Hadir'
        ]);



        return back()->with(
            'success',
            'Disposisi ditolak dan dikembalikan ke Subkoor'
        );
    }
}
