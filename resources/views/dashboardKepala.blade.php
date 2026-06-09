<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="row">

        {{-- KIRI --}}
        <div class="col-lg-9">
            <div class="row g-4 mb-4">
                <div class="col-md-4">

                    <div class="card shadow-sm">

                        <div class="card-body text-center">

                            <i class="bi bi-envelope fs-1"></i>

                            <h1>{{ $totalSuratBaru }}</h1>

                            <small>
                                Surat Baru
                            </small>

                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card shadow-sm">

                        <div class="card-body text-center">

                            <i class="bi bi-bell fs-1"></i>

                            <h1>{{ $totalNotifikasi }}</h1>

                            <small>
                                Notifikasi
                            </small>

                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card shadow-sm">

                        <div class="card-body text-center">

                            <i class="bi bi-calendar fs-1"></i>

                            <h1>100</h1>

                            <small>
                                Agenda Mendatang
                            </small>

                        </div>

                    </div>

                </div>

            </div>

            <h4 class="mb-3">

                <i class="bi bi-bell"></i>

                Notifikasi Terbaru

            </h4>

            @forelse($notifikasi as $surat)
                <div class="card mb-3 shadow-sm">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h6 class="fw-bold">
                                    Surat Terverifikasi
                                </h6>

                                <p class="mb-1">
                                    {{ $surat->perihal }}
                                </p>

                                <small class="text-muted">
                                    {{ $surat->asal_surat }}
                                </small>

                            </div>

                            <button class="btn btn-dark btn-sm" data-bs-toggle="modal"
                                data-bs-target="#detailModal{{ $surat->id_surat }}">

                                Detail

                            </button>

                        </div>

                    </div>

                </div>

            @empty

                <div class="alert alert-secondary">

                    Belum ada surat terverifikasi.

                </div>
            @endforelse
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


    @foreach ($notifikasi as $surat)
        <div class="modal fade" id="detailModal{{ $surat->id_surat }}" tabindex="-1">

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Detail Surat
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <p>
                            <strong>Perihal:</strong>
                            {{ $surat->perihal }}
                        </p>

                        <p>
                            <strong>Nomor Surat:</strong>
                            {{ $surat->nomor_surat }}
                        </p>

                        <p>
                            <strong>Asal Surat:</strong>
                            {{ $surat->asal_surat }}
                        </p>

                        <p>
                            <strong>Jenis Surat:</strong>
                            {{ $surat->jenis_surat }}
                        </p>

                    </div>

                </div>

            </div>

        </div>
    @endforeach
</x-layout>
