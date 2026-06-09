<x-layout :role="$role">

    <x-slot:title>
        {{ $title }}
    </x-slot:title>


    <div class="container-fluid">

        <div class="row">

            {{-- KONTEN UTAMA --}}
            <div class="col-lg-9">
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
                <h3 class="fw-bold mb-4">
                    Surat Masuk dan Disposisi
                </h3>
                <div class="border border-dark rounded-3 overflow-hidden shadow-sm">
                    <div class="table-responsive">

                        <table class="table table-sm table-borderless align-middle text-center mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold fs-6">Nomor Surat</th>
                                    <th class="fw-semibold fs-6">Pengirim</th>
                                    <th class="fw-semibold fs-6">Perihal</th>
                                    <th class="fw-semibold fs-6">Prioritas</th>
                                    <th class="fw-semibold fs-6">Tanggal Kegiatan</th>
                                    <th class="fw-semibold fs-6">Detail Surat</th>
                                    <th class="fw-semibold fs-6">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="fs-6 bg-white">

                                @forelse ($suratMasuk as $surat)
                                    <tr style="border-bottom:1px solid #dee2e6;">

                                        <td>
                                            {{ $surat->nomor_surat }}
                                        </td>

                                        <td>
                                            {{ $surat->asal_surat }}
                                        </td>

                                        <td>
                                            {{ $surat->perihal }}
                                        </td>

                                        <td>

                                            @if ($surat->prioritas == 'Tinggi')
                                                <span class="badge bg-danger">
                                                    Tinggi
                                                </span>
                                            @elseif($surat->prioritas == 'Sedang')
                                                <span class="badge bg-warning text-dark">
                                                    Sedang
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    Rendah
                                                </span>
                                            @endif

                                        </td>

                                        <td>

                                            @if ($surat->tanggal_kegiatan)
                                                {{ \Carbon\Carbon::parse($surat->tanggal_kegiatan)->format('d-m-Y') }}
                                            @else
                                                -
                                            @endif

                                        </td>
                                        <td>
                                            <button class="btn btn-dark btn-sm" style="width:100px;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $surat->id_surat }}">

                                                Detail

                                            </button>
                                        </td>
                                        <td>

                                            <div class="d-flex flex-column align-items-center gap-1">



                                                <button class="btn btn-primary btn-sm" style="width:100px;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#disposisiModal{{ $surat->id_surat }}">

                                                    Disposisi

                                                </button>

                                                <form action="#" method="POST">

                                                    @csrf

                                                    <button type="submit" class="btn btn-success btn-sm"
                                                        style="width:100px;">

                                                        Hadir

                                                    </button>

                                                </form>

                                                <form action="#" method="POST">

                                                    @csrf

                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        style="width:100px;">

                                                        Tolak

                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="6">

                                            Belum ada surat masuk.

                                        </td>

                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
            {{-- SIDEBAR KANAN --}}
            <div class="col-lg-3 ps-lg-4">

                <h4 class="fw-bold mb-3 fs-5">
                    Ringkasan Agenda dan Peserta
                </h4>

                <div class="card border-0 mb-3 shadow-sm" style="background-color: #f4f5f7; border-radius: 12px;">

                    <div class="card-body p-3">

                        <h6 class="fw-bold mb-0 text-dark">
                            Meeting 1:
                        </h6>

                        <h6 class="fw-bold mb-2 text-dark">
                            AG-20231015-001
                        </h6>

                        <p class="text-muted mb-0" style="font-size: 0.85rem;">

                            Peserta: Budi W. (Keuangan),
                            Susi A. (HR),
                            Andi R. (Operasional)

                        </p>

                    </div>

                </div>

                <div class="card border-0 mb-3 shadow-sm" style="background-color: #f4f5f7; border-radius: 12px;">

                    <div class="card-body p-3">

                        <h6 class="fw-bold mb-0 text-dark">
                            Meeting 2:
                        </h6>

                        <h6 class="fw-bold mb-2 text-dark">
                            AG-20231015-002
                        </h6>

                        <p class="text-muted mb-0" style="font-size: 0.85rem;">

                            Peserta: Budi W. (Keuangan),
                            Susi A. (HR),
                            Andi R. (Operasional)

                        </p>

                    </div>

                </div>

            </div>

        </div>
    </div>
    @foreach ($suratMasuk as $surat)
        <div class="modal fade" id="detailModal{{ $surat->id_surat }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header">
                        <h5 class="modal-title"> Detail Surat </h5> <button type="button" class="btn-close"
                            data-bs-dismiss="modal"> </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h4 class="mb-1 fw-semibold"> {{ $surat->perihal }} </h4> <small class="text-muted">
                                    {{ $surat->nomor_surat }} </small>
                            </div>

                            @if ($surat->status == 'Terverifikasi')
                                <span class="badge bg-success px-3 py-2" style="width:110px;font-size:0.85rem;">

                                    Terverifikasi

                                </span>
                            @else
                                <span class="badge bg-danger px-3 py-2" style="width:110px;font-size:0.85rem;">

                                    Ditolak

                                </span>
                            @endif

                        </div>
                        <hr>
                        <div class="mb-4">
                            <div class="text-uppercase text-secondary small fw-semibold mb-3"> Informasi Surat
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
                                    <div> {{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d M Y') }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small">Jenis Surat</div>
                                    <div>{{ $surat->jenis_surat }}</div>
                                </div>
                                <div class="col-md-6">

                                    <div class="text-secondary small">
                                        Prioritas
                                    </div>

                                    <div>

                                        <td>

                                            @if ($surat->prioritas == 'Tinggi')
                                                <span class="badge bg-danger px-3 py-2"
                                                    style="width:110px;font-size:0.85rem;">

                                                    Tinggi

                                                </span>
                                            @elseif($surat->prioritas == 'Sedang')
                                                <span class="badge bg-warning text-dark px-3 py-2"
                                                    style="width:110px;font-size:0.85rem;">

                                                    Sedang

                                                </span>
                                            @else
                                                <span class="badge bg-success px-3 py-2"
                                                    style="width:110px;font-size:0.85rem;">

                                                    Rendah

                                                </span>
                                            @endif

                                        </td>

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="text-secondary small">
                                        Tanggal Verifikasi
                                    </div>

                                    <div>

                                        {{ \Carbon\Carbon::parse($surat->tanggal_verifikasi)->format('d M Y H:i') }}

                                    </div>

                                </div>
                            </div>
                        </div>
                        @if ($surat->jenis_surat == 'Undangan')
                            <hr>
                            <div class="mb-4">
                                <div class="text-uppercase text-secondary small fw-semibold mb-3"> Informasi
                                    Kegiatan

                                </div>
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="text-secondary small">Tanggal Surat</div>
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
                            <div class="text-uppercase text-secondary small fw-semibold mb-3"> Lampiran Surat
                            </div>
                            <div class="border rounded-3 p-3 bg-body-tertiary">
                                <div class="d-flex align-items-center"> <i
                                        class="bi bi-file-earmark-pdf-fill text-danger fs-1 me-3"></i>
                                    <div>
                                        <div class="fw-semibold"> {{ $surat->file_scan }} </div> <small
                                            class="text-muted"> Berkas Scan Surat </small>
                                    </div>
                                </div> <a href="{{ asset('storage/surat/' . $surat->file_scan) }}" target="_blank"
                                    class="btn btn-primary btn-sm mt-3"> <i class="bi bi-eye me-1"></i> Lihat File
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endforeach
</x-layout>
