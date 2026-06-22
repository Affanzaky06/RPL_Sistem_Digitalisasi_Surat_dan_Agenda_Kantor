<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\Pegawai; // Wajib dipanggil untuk metrik card
use App\Models\Surat;   // Wajib dipanggil untuk agenda bawah
use App\Models\Bidang;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class KepegawaianController extends Controller
{
    public function index()
    {
        // 1. Tangkap data user yang sedang login
        $user = Auth::user();
        $role = 'Kepegawaian';

        // =================================================================
        // 2. DATA METRIK UNTUK 3 CARD DI ATAS (Data HR/SDM)
        // =================================================================
        
        // A. Total seluruh pegawai yang terdaftar
        $totalPegawai = Pegawai::count();

        // B. Total bidang (Mengambil jumlah id_bidang unik yang tidak null)
        // Jika Anda punya Model Bidang terpisah, bisa diganti jadi Bidang::count();
        $totalBidang = Pegawai::whereNotNull('id_bidang')->distinct('id_bidang')->count();

        // C. Jumlah pembaruan data pegawai di bulan dan tahun ini
        $pembaruanBulanIni = Pegawai::whereMonth('updated_at', Carbon::now()->month)
                                    ->whereYear('updated_at', Carbon::now()->year)
                                    ->count();

        // =================================================================
        // 3. DATA AGENDA UNTUK BAGIAN BAWAH
        // =================================================================
        
        // Menampilkan 3 agenda kantor terdekat
        $ringkasanAgenda = \App\Models\Agenda::with(['surat', 'peserta.pegawai'])
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

        // =================================================================
        // 4. LEMPAR SEMUA DATA KE VIEW BLADE
        // =================================================================
        return view('dashboardKepeg', [
            'title' => 'Dashboard Kepegawaian',
            'role' => $role,
            'user' => $user,
            
            // Variabel untuk Card
            'totalPegawai' => $totalPegawai,
            'totalBidang' => $totalBidang,
            'pembaruanBulanIni' => $pembaruanBulanIni,
            
            // Variabel untuk List Bawah
            'ringkasanAgenda' => $ringkasanAgenda
        ]);
    }

    // MENGAMBIL HALAMAN FORM INPUT PEGAWAI
    public function inputPegawai()
    {
        $user = Auth::user();
        $role = 'Kepegawaian';

        $ringkasanAgenda = \App\Models\Agenda::with(['surat', 'peserta.pegawai'])
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

        // 1. Ambil data bidang dan jabatan dari database
        $semuaBidang = \App\Models\Bidang::all();
        $semuaJabatan = \App\Models\Jabatan::all();

        return view('inputPegawai', [
            'title' => 'Input Data Pegawai',
            'role' => $role,
            'ringkasanAgenda' => $ringkasanAgenda,
            // 2. Lempar datanya ke file Blade
            'semuaBidang' => $semuaBidang,
            'semuaJabatan' => $semuaJabatan
        ]);
    }

    // MEMPROSES PENYIMPANAN DATA KE DATABASE
    public function storePegawai(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'nip' => 'required|unique:pegawai,nip|max:50',
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'email' => 'nullable|email|unique:pegawai,email',
            'no_telp' => 'nullable|string|max:20',
            'bidang' => 'required',
            'jabatan' => 'required',
            'berkas_pegawai' => 'required|file|mimes:pdf,jpg,jpeg|max:2048', // Maksimal 2MB
        ], [
            'nip.unique' => 'NIP ini sudah terdaftar di sistem.',
            'berkas_pegawai.mimes' => 'Format file harus PDF, JPG, atau JPEG.'
        ]);

        // 2. Proses Upload File
        $filePath = null;
        if ($request->hasFile('berkas_pegawai')) {
            $file = $request->file('berkas_pegawai');
            // Format nama file: NIP_NamaFileAsli
            $filename = $request->nip . '_' . time() . '.' . $file->getClientOriginalExtension();
            // Simpan ke folder storage/app/public/berkas_pegawai
            $filePath = $file->storeAs('berkas_pegawai', $filename, 'public');
        }

        // 3. Simpan ke database
        Pegawai::create([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'tanggal_lahir' => $request->tanggal_lahir,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'id_bidang' => $request->bidang,
            'id_jabatan' => $request->jabatan,
            'foto_profil' => $filePath, // Kita simpan path file ke kolom ini sesuai migration
            'password' => Hash::make('Pegawai123'), // Password default
            // nip_atasan dibiarkan null dulu untuk saat ini
        ]);

        // 4. Kembali ke halaman form dengan pesan sukses
        return redirect()->back()->with('success', 'Data pegawai berhasil ditambahkan!');
    }

    // MENAMPILKAN HALAMAN LIST PEGAWAI (Dengan Search & Sort)
    public function listPegawai(Request $request)
    {
        $user = Auth::user();
        $role = 'Kepegawaian';

        $ringkasanAgenda = \App\Models\Agenda::with(['surat', 'peserta.pegawai'])
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

        $search = $request->input('search');
        $sort = $request->input('sort', 'default'); // Ambil nilai sort

        $queryPegawai = Pegawai::with(['jabatan', 'bidang']);

        // Fitur Searching
        if ($search) {
            $queryPegawai->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nip', 'LIKE', "%{$search}%");
            });
        }

        // Fitur Sorting
        if ($sort === 'nama_asc') {
            $queryPegawai->orderBy('nama', 'asc');
        } elseif ($sort === 'nama_desc') {
            $queryPegawai->orderBy('nama', 'desc');
        } else {
            $queryPegawai->latest(); // Default: yang terbaru ditambahkan
        }

        $daftarPegawai = $queryPegawai->paginate(5);

        $semuaBidang = Bidang::all();
        $semuaJabatan = Jabatan::all();

        return view('listPegawai', [
            'title' => 'List Pegawai',
            'role' => $role,
            'ringkasanAgenda' => $ringkasanAgenda,
            'daftarPegawai' => $daftarPegawai,
            'search' => $search,
            'sort' => $sort, // Kirim data sort ke view
            'semuaBidang' => $semuaBidang,
            'semuaJabatan' => $semuaJabatan
        ]);
    }

    // MEMPROSES UPDATE DATA PEGAWAI DARI MODAL
public function updatePegawai(Request $request, $nip)
    {
        // 1. Ambil data pegawai yang lama dari database
        $pegawai = Pegawai::where('nip', $nip)->firstOrFail();

        // 2. Validasi input form
        $request->validate([
            'nip' => 'required|string|max:50|unique:pegawai,nip,' . $nip . ',nip', 
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'email' => 'nullable|email|unique:pegawai,email,' . $nip . ',nip',
            'no_telp' => 'nullable|string|max:20',
            'bidang' => 'required',
            'jabatan' => 'required',
            'berkas_pegawai' => 'nullable|file|mimes:pdf,jpg,jpeg|max:2048',
        ]);

        // 3. KUNCI RAHASIA: Amankan nama file lama sebagai nilai default!
        $filePath = $pegawai->foto_profil;

        // 4. Jika user MENGUNGGAH file baru, barulah kita timpa variabel $filePath di atas
        if ($request->hasFile('berkas_pegawai')) {
            $file = $request->file('berkas_pegawai');
            $filename = $request->nip . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('berkas_pegawai', $filename, 'public');
            
            // (Opsional) Jika ingin otomatis menghapus file lama di server agar storage tidak penuh:
            // if ($pegawai->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists($pegawai->foto_profil)) {
            //     \Illuminate\Support\Facades\Storage::disk('public')->delete($pegawai->foto_profil);
            // }
        }

        // 5. Update database menggunakan Query Builder
        Pegawai::where('nip', $nip)->update([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'tanggal_lahir' => $request->tanggal_lahir,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'id_bidang' => $request->bidang,
            'id_jabatan' => $request->jabatan,
            'foto_profil' => $filePath // Sekarang ini pasti aman, berisi file lama ATAU file baru
        ]);

        return redirect()->back()->with('success', 'Data pegawai berhasil diperbarui!');
    }
    // MEMPROSES HAPUS DATA PEGAWAI
    public function destroyPegawai($nip)
    {
        $pegawai = Pegawai::where('nip', $nip)->firstOrFail();

        // Cek apakah pegawai sedang terlibat di surat disposisi atau agenda
        $usedInDisposisi = \App\Models\Disposisi::where('nip_pemberi', $nip)->orWhere('nip_penerima', $nip)->exists();
        $usedInPeserta = \App\Models\Peserta::where('nip', $nip)->exists();

        if ($usedInDisposisi || $usedInPeserta) {
            return redirect()->back()->with('error', 'Data pegawai tidak dapat dihapus karena masih terkait dengan Riwayat Disposisi atau Agenda Kegiatan!');
        }

        $pegawai->delete();

        return redirect()->back()->with('success', 'Data pegawai berhasil dihapus!');
    }

    public function resetPassword($nip)
    {
        $pegawai = Pegawai::where('nip', $nip)->firstOrFail();
        $pegawai->update([
            'password' => \Illuminate\Support\Facades\Hash::make('Pegawai123')
        ]);

        return redirect()->back()->with('success', 'Password pegawai ' . $pegawai->nama . ' berhasil direset menjadi: Pegawai123');
    }

    // =================================================================
    // CRUD DATA BIDANG
    // =================================================================

    public function indexBidang(Request $request)
    {
        $user = Auth::user();
        $role = 'Kepegawaian';

        $search = $request->input('search');
        $query = Bidang::query();

        if ($search) {
            $query->where('nama_bidang', 'LIKE', "%{$search}%");
        }

        $daftarBidang = $query->orderBy('id_bidang', 'asc')->paginate(10);

        return view('kepegawaian.bidang', compact('daftarBidang', 'search', 'role'));
    }

    public function storeBidang(Request $request)
    {
        $request->validate([
            'nama_bidang' => 'required|string|max:255'
        ]);

        // Auto-generate ID Bidang (B001, B002, dst)
        $lastBidang = Bidang::orderBy('id_bidang', 'desc')->first();
        if ($lastBidang) {
            $lastNumber = (int) substr($lastBidang->id_bidang, 1);
            $newId = 'B' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newId = 'B001';
        }

        Bidang::create([
            'id_bidang' => $newId,
            'nama_bidang' => $request->nama_bidang
        ]);

        return redirect()->back()->with('success', 'Data Bidang berhasil ditambahkan!');
    }

    public function updateBidang(Request $request, $id_bidang)
    {
        $request->validate([
            'nama_bidang' => 'required|string|max:255'
        ]);

        $bidang = Bidang::findOrFail($id_bidang);
        $bidang->update([
            'nama_bidang' => $request->nama_bidang
        ]);

        return redirect()->back()->with('success', 'Data Bidang berhasil diperbarui!');
    }

    public function destroyBidang($id_bidang)
    {
        // Cek apakah bidang sedang digunakan oleh pegawai
        $isUsed = Pegawai::where('id_bidang', $id_bidang)->exists();

        if ($isUsed) {
            return redirect()->back()->with('error', 'Bidang tidak dapat dihapus karena masih digunakan oleh pegawai!');
        }

        $bidang = Bidang::findOrFail($id_bidang);
        $bidang->delete();

        return redirect()->back()->with('success', 'Data Bidang berhasil dihapus!');
    }
}