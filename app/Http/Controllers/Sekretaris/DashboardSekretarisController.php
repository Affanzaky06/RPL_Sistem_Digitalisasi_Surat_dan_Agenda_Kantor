<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class DashboardSekretarisController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $notifikasi = Surat::with(['disposisi' => function ($q) use ($user) {
                $q->where('nip_penerima', $user->nip)
                    ->latest('id_disposisi');
            }])
            ->where(function ($query) use ($user) {
                $query->where('status', 'Menunggu Verifikasi')
                    ->orWhereHas('disposisi', function ($q) use ($user) {
                        $q->where('nip_penerima', $user->nip)
                            ->whereIn('status', [
                                'Menunggu Konfirmasi',
                                'Belum Dibaca',
                                'Dalam Proses'
                            ]);
                    });
            })
            ->latest('updated_at')
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
