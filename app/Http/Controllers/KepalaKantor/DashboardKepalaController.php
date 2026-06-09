<?php

namespace App\Http\Controllers\KepalaKantor;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;

class DashboardKepalaController extends Controller
{
    public function index()
    {
        $notifikasi = Surat::query()
            ->where('status', 'Terverifikasi')
            ->latest('tanggal_verifikasi')
            ->take(5)
            ->get();

        $totalSuratBaru = Surat::whereDate(
            'tanggal_verifikasi',
            today()
        )->count();

        $totalNotifikasi = $notifikasi->count();

        return view(
            'dashboardKepala',
            [
                'title' => 'Kepala',
                'role' => 'Kepala',

                'notifikasi' => $notifikasi,

                'totalSuratBaru' => $totalSuratBaru,
                'totalNotifikasi' => $totalNotifikasi
            ]
        );
    }
}
