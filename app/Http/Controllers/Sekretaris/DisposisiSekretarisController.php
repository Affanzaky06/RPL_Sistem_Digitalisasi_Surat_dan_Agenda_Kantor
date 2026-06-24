<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use App\Models\Surat;
use App\Models\Agenda;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposisiSekretarisController extends Controller
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
        $ringkasanAgenda = \App\Models\Agenda::whereHas('peserta', function ($q) use ($user) {
            $q->where('nip', $user->nip);
                $q->where('status_kehadiran', 'Hadir');
        })
            ->with(['surat', 'peserta.pegawai']) // Wajib agar tidak null di view
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
        $suratMasuk = Surat::with([
            'disposisi.pemberi.bidang'
        ])
            ->whereHas('disposisi', function ($q) {

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
            ->latest()
            ->paginate(10);

        return view(
            'disposisi.disposisiSekretaris',
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

        // Cek IDOR: Pastikan Sekretaris benar-benar menerima disposisi surat ini
        $cekDisposisi = Disposisi::where('id_surat', $id_surat)
            ->where('nip_penerima', $user->nip)
            ->latest('id_disposisi')
            ->first();

        if (!$cekDisposisi) {
            return back()->with('error', 'Akses ditolak: Anda tidak memiliki wewenang untuk surat ini.');
        }

        // Cek Bentrok Jadwal
        if ($surat->tanggal_kegiatan && $surat->waktu_mulai_kegiatan && $surat->waktu_selesai_kegiatan) {
            $bentrok = \App\Models\Agenda::checkConflict(
                $user->nip, 
                $surat->tanggal_kegiatan, 
                $surat->waktu_mulai_kegiatan, 
                $surat->waktu_selesai_kegiatan
            );
            
            if ($bentrok) {
                return back()->with('error', 'Tidak bisa menghadiri. Jadwal bertabrakan dengan acara: ' . $bentrok->nama_kegiatan . ' (' . \Carbon\Carbon::parse($bentrok->waktu_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($bentrok->waktu_selesai)->format('H:i') . '). Silakan tolak surat ini atau batalkan kehadiran acara sebelumnya jika acara ini lebih penting.');
            }
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $cekDisposisi->update([
                'status' => 'Hadir'
            ]);

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

            // MASUKKAN SEKRETARIS SEBAGAI PESERTA PASTI HADIR
            Peserta::updateOrCreate(
                [
                    'id_agenda' => $agenda->id_agenda,
                    'nip' => $user->nip
                ],
                [
                    'status_kehadiran' => 'Hadir'
                ]
            );

            \Illuminate\Support\Facades\DB::commit();
            return back()->with(
                'success',
                'Kehadiran berhasil dikonfirmasi'
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses data: ' . $e->getMessage());
        }
    }

    public function tolakDispo($id_surat)
    {
        request()->validate([
            'alasan_tolak' => 'required|string|max:1000',
        ]);

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
            'status' => 'Tidak Hadir',
            'catatan' => request('alasan_tolak'),
        ]);

        return back()->with(
            'success',
            'Disposisi ditolak dan dikembalikan ke Kepala'
        );
    }
}
