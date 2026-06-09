<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;

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

        return view(
            'sekretaris.verifikasiSurat',
            [
                'title' => 'Sekretaris',
                'role' => 'Sekretaris',
                'suratMasuk' => $suratMasuk
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

                $q->where(
                    'nomor_surat',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'perihal',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'asal_surat',
                        'like',
                        "%{$search}%"
                    );
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

        return view(
            'sekretaris.riwayatVerifikasi',
            [
                'title' => 'Sekretaris',
                'role' => 'Sekretaris',
                'suratMasuk' => $suratMasuk
            ]
        );
    }
}
