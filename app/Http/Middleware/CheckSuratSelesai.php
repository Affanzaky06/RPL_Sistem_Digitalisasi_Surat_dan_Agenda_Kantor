<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckSuratSelesai
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $now = \Carbon\Carbon::now();
            $suratSelesai = \App\Models\Surat::whereNotNull('tanggal_kegiatan')
                ->where(function($q) use ($now) {
                    $q->where('tanggal_kegiatan', '<', $now->toDateString())
                      ->orWhere(function($q2) use ($now) {
                          $q2->where('tanggal_kegiatan', '=', $now->toDateString())
                             ->where('waktu_selesai_kegiatan', '<', $now->toTimeString());
                      });
                })->pluck('id_surat');

            if ($suratSelesai->isNotEmpty()) {
                \App\Models\Surat::whereIn('id_surat', $suratSelesai)
                    ->whereNotIn('status', ['Selesai', 'Ditolak', 'Selesai (Kegiatan Berlalu)'])
                    ->update(['status' => 'Selesai']);
                    
                \App\Models\Disposisi::whereIn('id_surat', $suratSelesai)
                    ->whereNotIn('status', ['Selesai', 'Dimaklumi'])
                    ->update(['status' => 'Selesai']);
            }
        } catch (\Exception $e) {
            // Abaikan jika error agar tidak mengganggu request utama
        }

        return $next($request);
    }
}
