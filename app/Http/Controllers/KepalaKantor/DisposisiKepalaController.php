<?php

namespace App\Http\Controllers\KepalaKantor;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;

class DisposisiKepalaController extends Controller
{
    public function index()
    {
        $suratMasuk = Surat::query()
            ->where('status', 'Terverifikasi')
            ->latest('tanggal_verifikasi')
            ->paginate(10);

        return view(
            'disposisi.disposisiKepala',
            [
                'title' => 'Kepala',
                'role' => 'Kepala',
                'suratMasuk' => $suratMasuk
            ]
        );
    }
}
