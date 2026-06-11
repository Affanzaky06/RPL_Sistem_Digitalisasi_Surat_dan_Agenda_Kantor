<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\Surat;
use Illuminate\Http\Request;

class laporanPemantauanController extends Controller
{

    public function index()
    {
        $laporan = Disposisi::with([
            'penerima.bidang'
        ])
            ->latest()
            ->paginate(10);

        return view(
            'laporanPemantauan',
            [
                'title' => 'Kepala',
                'role' => 'Kepala',
                'laporan' => $laporan,
                'ringkasanAgenda' => collect()
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
