<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
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

        $routeBatalDisposisi = $routeMap[$user->id_jabatan] ?? null;

        return view(
            'laporanPemantauan',
            [
                'title' => $role,
                'role' => $role,
                'laporan' => $laporan,
                'ringkasanAgenda' => collect(),
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
}
