<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="container-fluid p-0">
        <div class="row">

            <div class="col-lg-9 pe-lg-4">
                @if (session('success'))
                    <div class="position-fixed top-0 end-0 p-3" style="z-index:9999;">
                        <div class="toast show border-0 shadow">
                            <div class="toast-body">
                                <span class="text-success">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                </span>
                                {{ session('success') }}
                            </div>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="position-fixed top-0 end-0 p-3" style="z-index:9999;">
                        <div class="toast show border-0 shadow">
                            <div class="toast-body">
                                <span class="text-danger">
                                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                                </span>
                                {{ session('error') }}
                            </div>
                        </div>
                    </div>
                @endif
                <script>
                    setTimeout(() => {
                        document.querySelectorAll('.toast').forEach(el => {
                            el.remove();
                        });
                    }, 6000);
                </script>
                <h3 class="fw-bold mb-3 fs-4">Riwayat Input Surat</h3>

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <form method="GET" class="d-flex gap-2" style="width: 100%; max-width: 550px;">

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nomor surat, perihal, atau pengirim..."
                            class="form-control border-dark border-1 rounded-2">

                        <button type="submit"
                            class="btn bg-white border-dark border-1 rounded-2 d-flex align-items-center gap-2 px-4 text-dark text-nowrap">

                            <i class="bi bi-search"></i>
                            Cari

                        </button>

                    </form>

                    <form method="GET">

                        <input type="hidden" name="search" value="{{ request('search') }}">

                        <select name="sort" class="form-select border-dark rounded-2 py-1" style="width:110px;"
                            onchange="this.form.submit()">

                            <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>
                                Default
                            </option>

                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>
                                Terbaru
                            </option>

                            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>
                                Terlama
                            </option>

                        </select>

                    </form>

                </div>



                <div class="border border-dark rounded-3 overflow-hidden mb-3 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless align-middle text-center mb-0"
                            style="border-style: hidden;">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold fs-6">Pengirim</th>
                                    <th class="fw-semibold fs-6">Nomor</th>
                                    <th class="fw-semibold fs-6">Perihal</th>
                                    <th class="fw-semibold fs-6">Tanggal</th>
                                    <th class="fw-semibold fs-6">Status</th>
                                    <th class="fw-semibold fs-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="fs-6 bg-white">

                                @forelse ($suratMasuk as $surat)
                                    <tr style="border-bottom: 1px solid #dee2e6;">

                                        <td class="text-dark py-2">
                                            {{ $surat->asal_surat }}
                                        </td>

                                        <td class="text-dark py-2">
                                            {{ $surat->nomor_surat }}
                                        </td>

                                        <td class="text-dark py-2">
                                            {{ $surat->perihal }}
                                        </td>

                                        <td class="text-dark py-2">
                                            {{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d-m-Y') }}
                                        </td>

                                        <td class="text-dark py-2">
                                            @if ($surat->status == 'Menunggu Verifikasi')
                                                <span class="badge bg-warning text-dark">
                                                    Menunggu Verifikasi
                                                </span>
                                            @elseif($surat->status == 'Terverifikasi')
                                                <span class="badge bg-success">
                                                    Terverifikasi
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    {{ $surat->status }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="py-2">

                                            <div class="d-flex flex-column gap-1 px-2">

                                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#detailModal{{ $surat->id_surat }}">
                                                    Detail
                                                </button>

                                                @if ($surat->status === 'Menunggu Verifikasi')
                                                    <button class="btn btn-warning btn-sm rounded-1 text-white"
                                                        style="font-size:0.7rem;" data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $surat->id_surat }}">
                                                        Edit
                                                    </button>

                                                    <form action="{{ route('surat.destroy', $surat->id_surat) }}"
                                                        method="POST" class="m-0">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                            class="btn btn-danger btn-sm rounded-1 w-100"
                                                            style="font-size:0.7rem;"
                                                            onclick="return confirm('Yakin ingin menghapus surat ini?')">

                                                            Hapus

                                                        </button>

                                                    </form>
                                                @endif

                                            </div>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            Belum ada data surat.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-3">
                    {{ $suratMasuk->links() }}
                </div>

            </div>

            <div class="col-lg-3 ps-lg-4">
                <h4 class="fw-bold mb-3 fs-5">
                    Ringkasan Agenda dan Peserta
                </h4>

                <div class="card border-0 mb-3 shadow-sm" style="background-color: #f4f5f7; border-radius: 12px;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-0 text-dark">Meeting 1:</h6>
                        <h6 class="fw-bold mb-2 text-dark">AG-20231015-001</h6>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">
                            Peserta: Budi W. (Keuangan), Susi A. (HR), Andi R. (Operasional)
                        </p>
                    </div>
                </div>

                <div class="card border-0 mb-3 shadow-sm" style="background-color: #f4f5f7; border-radius: 12px;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-0 text-dark">Meeting 2:</h6>
                        <h6 class="fw-bold mb-2 text-dark">AG-20231015-001</h6>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">
                            Peserta: Budi W. (Keuangan), Susi A. (HR), Andi R. (Operasional)
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL DETAIL --}}
    @foreach ($suratMasuk as $surat)
        <div class="modal fade" id="detailModal{{ $surat->id_surat }}" tabindex="-1" aria-hidden="true">

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content border-0 shadow rounded-4">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Detail Surat
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>

                    <div class="modal-body p-4">

                        <div class="d-flex justify-content-between align-items-start mb-4">

                            <div>
                                <h4 class="mb-1 fw-semibold">
                                    {{ $surat->perihal }}
                                </h4>

                                <small class="text-muted">
                                    {{ $surat->nomor_surat }}
                                </small>
                            </div>

                            @if ($surat->status == 'Menunggu Verifikasi')
                                <span class="badge rounded-pill text-bg-warning px-3 py-2">
                                    Menunggu Verifikasi
                                </span>
                            @elseif ($surat->status == 'Terverifikasi')
                                <span class="badge rounded-pill text-bg-success px-3 py-2">
                                    Terverifikasi
                                </span>
                            @endif

                        </div>

                        <hr>

                        <div class="mb-4">

                            <div class="text-uppercase text-secondary small fw-semibold mb-3">
                                Informasi Surat
                            </div>

                            <div class="row g-4">

                                <div class="col-md-6">
                                    <div class="text-secondary small">Nomor Surat</div>
                                    <div>{{ $surat->nomor_surat }}</div>
                                </div>

                                <div class="col-md-6">
                                    <div class="text-secondary small">Asal Surat</div>
                                    <div>{{ $surat->asal_surat }}</div>
                                </div>

                                <div class="col-md-6">
                                    <div class="text-secondary small">Tanggal Surat</div>
                                    <div>
                                        {{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d M Y') }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="text-secondary small">Jenis Surat</div>
                                    <div>{{ $surat->jenis_surat }}</div>
                                </div>

                            </div>

                        </div>

                        @if ($surat->jenis_surat == 'Undangan')
                            <hr>

                            <div class="mb-4">

                                <div class="text-uppercase text-secondary small fw-semibold mb-3">
                                    Informasi Kegiatan
                                </div>

                                <div class="row g-4">

                                    <div class="col-md-4">
                                        <div class="text-secondary small">Tanggal</div>
                                        <div>
                                            {{ \Carbon\Carbon::parse($surat->tanggal_kegiatan)->format('d M Y') }}
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="text-secondary small">Waktu Mulai</div>
                                        <div>{{ $surat->waktu_mulai_kegiatan }}</div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="text-secondary small">Waktu Selesai</div>
                                        <div>{{ $surat->waktu_selesai_kegiatan }}</div>
                                    </div>

                                    <div class="col-12">
                                        <div class="text-secondary small">Lokasi</div>
                                        <div>{{ $surat->lokasi_kegiatan }}</div>
                                    </div>

                                </div>

                            </div>
                        @endif

                        <hr>

                        <div>

                            <div class="text-uppercase text-secondary small fw-semibold mb-3">
                                Lampiran Surat
                            </div>

                            <div class="border rounded-3 p-3 bg-body-tertiary">

                                <div class="d-flex align-items-center">

                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-1 me-3"></i>

                                    <div>

                                        <div class="fw-semibold">
                                            {{ $surat->file_scan }}
                                        </div>

                                        <small class="text-muted">
                                            Berkas Scan Surat
                                        </small>

                                    </div>

                                </div>

                                <a href="{{ asset('storage/surat/' . $surat->file_scan) }}" target="_blank"
                                    class="btn btn-primary btn-sm mt-3">

                                    <i class="bi bi-eye me-1"></i>
                                    Lihat File

                                </a>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endforeach
    @foreach ($suratMasuk as $surat)
        <div class="modal fade" id="editModal{{ $surat->id_surat }}" tabindex="-1">

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content border-0 shadow-sm rounded-4">

                    <form action="{{ route('surat.update', $surat->id_surat) }}" method="POST"
                        enctype="multipart/form-data">

                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">
                                Edit Surat
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>

                        <div class="modal-body">

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Perihal
                                    </label>

                                    <input type="text" name="perihal"
                                        value="{{ old('perihal', $surat->perihal) }}" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Nomor Surat
                                    </label>

                                    <input type="text" name="nomor_surat"
                                        value="{{ old('nomor_surat', $surat->nomor_surat) }}"
                                        class="form-control @error('nomor_surat') is-invalid @enderror">

                                    @error('nomor_surat')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Tanggal Surat
                                    </label>

                                    <input type="date" name="tanggal_surat"
                                        value="{{ old('tanggal_surat', $surat->tanggal_surat) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Tanggal Kegiatan
                                    </label>

                                    <input type="date" name="tanggal_kegiatan"
                                        value="{{ old('tanggal_kegiatan', $surat->tanggal_kegiatan) }}"
                                        class="form-control">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        Lokasi
                                    </label>

                                    <input type="text" name="lokasi_kegiatan"
                                        value="{{ old('lokasi_kegiatan', $surat->lokasi_kegiatan) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Waktu Mulai
                                    </label>

                                    <input type="time" name="waktu_mulai_kegiatan"
                                        value="{{ old('waktu_mulai_kegiatan', $surat->waktu_mulai_kegiatan) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Waktu Selesai
                                    </label>

                                    <input type="time" name="waktu_selesai_kegiatan"
                                        value="{{ old('waktu_selesai_kegiatan', $surat->waktu_selesai_kegiatan) }}"
                                        class="form-control">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        Asal Surat
                                    </label>

                                    <input type="text" name="asal_surat"
                                        value="{{ old('asal_surat', $surat->asal_surat) }}" class="form-control">
                                </div>

                                <div class="col-12">

                                    <label class="form-label">
                                        File Scan Surat
                                    </label>

                                    <div class="mb-2">

                                        <a href="{{ asset('storage/surat/' . $surat->file_scan) }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm">

                                            <i class="bi bi-eye"></i>
                                            Lihat File Saat Ini

                                        </a>

                                    </div>

                                    <input type="file" name="file_scan"
                                        class="form-control @error('file_scan') is-invalid @enderror"
                                        accept=".pdf,.jpg,.jpeg,.png">

                                    @error('file_scan')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <small class="text-muted">
                                        Kosongkan jika tidak ingin mengganti file.
                                    </small>

                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                                Batal

                            </button>

                            <button type="submit" class="btn btn-primary">

                                Simpan Perubahan

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>
    @endforeach

</x-layout>
