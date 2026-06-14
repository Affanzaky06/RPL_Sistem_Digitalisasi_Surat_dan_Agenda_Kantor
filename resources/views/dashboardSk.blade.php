<x-layout :role="$role">

    <x-slot:title>
        {{ $title }}
    </x-slot:title>


    <div class="row g-4 mb-4">

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body text-center">

                    <i class="bi bi-envelope fs-1"></i>

                    <h1>{{ $totalSurat }}</h1>

                    <small>
                        Total Surat Masuk
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body text-center">

                    <i class="bi bi-bell fs-1"></i>

                    <h1>{{ count($notifikasi) }}</h1>

                    <small>
                        Notifikasi
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body text-center">

                    <i class="bi bi-file-earmark-text fs-1"></i>

                    <h1>{{ $menungguVerifikasi }}</h1>

                    <small>
                        Menunggu Verifikasi
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

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <h6 class="fw-bold mb-1">
                            Surat Baru
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
            Belum ada surat menunggu verifikasi.
        </div>
    @endforelse

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
