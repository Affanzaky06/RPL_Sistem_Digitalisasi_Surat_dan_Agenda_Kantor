<?php

namespace App\Http\Controllers\Frontliner;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class SuratController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(
            [
                'jenis_surat' => 'required',
                'nomor_surat' => 'required|unique:surat,nomor_surat',
                'perihal' => 'required',
                'tanggal_surat' => 'required',
                'asal_surat' => 'required',
                'berkas_surat' => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
            ],
            [
                'nomor_surat.required' => 'Nomor surat wajib diisi.',
                'nomor_surat.unique' => 'Nomor surat sudah terdaftar.',
            ]
        );

        $namaFile = null;

        if ($request->hasFile('berkas_surat')) {

            $file = $request->file('berkas_surat');

            $namaFile = time() . '_' . $file->getClientOriginalName();

            $file->storeAs(
                'surat',
                $namaFile,
                'public'
            );
        }

        $surat = Surat::create([
            'perihal' => $request->perihal,
            'nomor_surat' => $request->nomor_surat,
            'jenis_surat' => $request->jenis_surat,
            'prioritas' => $request->prioritas,

            'tanggal_surat' => $request->tanggal_surat,

            'tanggal_kegiatan' => $request->tanggal_kegiatan,
            'lokasi_kegiatan' => $request->lokasi,
            'waktu_mulai_kegiatan' => $request->waktu_mulai,
            'waktu_selesai_kegiatan' => $request->waktu_selesai,

            'asal_surat' => $request->asal_surat,

            'status' => 'Menunggu Verifikasi',

            'file_scan' => $namaFile,

            'tanggal_verifikasi' => null
        ]);


        return redirect()->back()->with(
            'success',
            'Surat berhasil disimpan'
        );
    }

    public function index(Request $request)
    {
        $query = Surat::query();

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

        $suratMasuk = $query->paginate(10)->withQueryString();

        return view(
            'frontliner.RiwayatInput',
            [
                'title' => 'Riwayat Input Surat',
                'role' => 'Frontliner',
                'suratMasuk' => $suratMasuk
            ]
        );
    }

    public function update(Request $request, $id)
    {
        $surat = Surat::findOrFail($id);

        if ($surat->status !== 'Menunggu Verifikasi') {
            abort(403);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'nomor_surat' => 'required|unique:surat,nomor_surat,' . $id . ',id_surat',
                'perihal' => 'required',
                'tanggal_surat' => 'required',
                'asal_surat' => 'required',
                'file_scan' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120'
            ],
            [
                'nomor_surat.required' => 'Nomor surat wajib diisi.',
                'nomor_surat.unique' => 'Nomor surat sudah terdaftar.',
                'file_scan.mimes' => 'File harus PDF/JPG/JPEG/PNG.',
                'file_scan.max' => 'Ukuran file maksimal 5 MB.'
            ]
        );

        if ($validator->fails()) {

            return back()->with(
                'error',
                $validator->errors()->first()
            );
        }

        // reupload file
        if ($request->hasFile('file_scan')) {

            // hapus file lama (opsional)
            if (
                $surat->file_scan &&
                \Storage::disk('public')->exists('surat/' . $surat->file_scan)
            ) {

                \Storage::disk('public')
                    ->delete('surat/' . $surat->file_scan);
            }

            $fileName =
                time() . '_' .
                $request->file('file_scan')->getClientOriginalName();

            $request->file('file_scan')
                ->storeAs('surat', $fileName, 'public');
        } else {

            $fileName = $surat->file_scan;
        }

        $surat->update([
            'perihal' => $request->perihal,
            'nomor_surat' => $request->nomor_surat,
            'tanggal_surat' => $request->tanggal_surat,
            'tanggal_kegiatan' => $request->tanggal_kegiatan,
            'lokasi_kegiatan' => $request->lokasi_kegiatan,
            'waktu_mulai_kegiatan' => $request->waktu_mulai_kegiatan,
            'waktu_selesai_kegiatan' => $request->waktu_selesai_kegiatan,
            'asal_surat' => $request->asal_surat,
            'file_scan' => $fileName,
        ]);

        return back()->with(
            'success',
            'Surat berhasil diperbarui'
        );
    }

    public function destroy($id)
    {
        $surat = Surat::findOrFail($id);

        if ($surat->status !== 'Menunggu Verifikasi') {

            return back()->with(
                'error',
                'Surat yang sudah diverifikasi tidak dapat dihapus.'
            );
        }

        // hapus file scan
        if (
            $surat->file_scan &&
            \Storage::disk('public')->exists('surat/' . $surat->file_scan)
        ) {

            \Storage::disk('public')
                ->delete('surat/' . $surat->file_scan);
        }

        $surat->delete();

        return back()->with(
            'success',
            'Surat berhasil dihapus.'
        );
    }
}
