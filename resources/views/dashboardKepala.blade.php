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

                            <h1>{{ $totalAgenda }}</h1>

                            <small>
                                Agenda Mendatang
                            </small>

                        </div>

                    </div>

                </div>

            </div>
            <h4 class="mb-3">
                <i class="bi bi-bell"></i> Notifikasi Terbaru
            </h4>

            @forelse($notifikasi as $surat)
                <div class="card mb-3 shadow-sm position-relative">

                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" style="z-index: 10;"
                        aria-label="Close" onclick="this.closest('.card').remove()"></button>

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold text-success">
                                    <i class="bi bi-check-circle me-1"></i> Surat Terverifikasi
                                </h6>
                                <p class="mb-1 fw-medium">{{ $surat->perihal }}</p>
                                <small class="text-muted">{{ $surat->asal_surat }}</small>
                            </div>

                            <div class="me-4">
                                <button class="btn btn-dark btn-sm px-3" data-bs-toggle="modal"
                                    data-bs-target="#detailModal{{ $surat->id_surat }}">
                                    Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-secondary shadow-sm border-0">
                    <i class="bi bi-info-circle me-2"></i> Belum ada surat terverifikasi terbaru.
                </div>
            @endforelse
        </div>

        <x-card-agenda :ringkasanAgenda="$ringkasanAgenda" />


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
