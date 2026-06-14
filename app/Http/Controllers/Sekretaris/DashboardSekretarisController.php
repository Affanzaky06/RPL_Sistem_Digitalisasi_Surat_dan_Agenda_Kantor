<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;



class DashboardSekretarisController extends Controller
{
    public function index()
    {
        $notifikasi = Surat::where(
            'status',
            'Menunggu Verifikasi'
        )
            ->latest()
            ->take(5)
            ->get();
            
        $totalSurat = Surat::count(); // Total semua surat masuk di sistem
        $menungguVerifikasi = Surat::where('status', 'Menunggu Verifikasi')->count();

        return view(
            'dashboardSk',
            [
                'title' => 'Sekretaris',
                'role' => 'Sekretaris',
                'notifikasi' => $notifikasi,
                'totalSurat' => $totalSurat, // Kirim variabel total surat
                'menungguVerifikasi' => $menungguVerifikasi
            ]
        );
    }
}
