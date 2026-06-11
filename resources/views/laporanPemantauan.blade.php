<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container-fluid">
        <div class="row">

            <!-- KIRI : TABEL -->
            <div class="col-lg-9">

                <h1 class="mb-4">Laporan dan Pemantauan Surat</h1>

                <div class="border border-dark rounded-3 overflow-hidden shadow-sm">

                    <div class="table-responsive">

                        <table class="table table-sm table-borderless align-middle text-center mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th class="fw-semibold fs-6">
                                        Penerima Disposisi
                                    </th>

                                    <th class="fw-semibold fs-6">
                                        Tanggal Disposisi
                                    </th>

                                    <th class="fw-semibold fs-6">
                                        Catatan
                                    </th>

                                    <th class="fw-semibold fs-6">
                                        Status
                                    </th>

                                    <th class="fw-semibold fs-6">
                                        Aksi
                                    </th>

                                </tr>

                            </thead>

                            <tbody class="fs-6 bg-white">

                                @forelse ($laporan as $item)
                                    <tr style="border-bottom:1px solid #dee2e6;">

                                        <td>

                                            <div class="fw-semibold">
                                                {{ $item->penerima->nama ?? '-' }}
                                            </div>

                                            <small class="text-muted">

                                                @if ($item->penerima->id_jabatan == 'J006')
                                                    Sekretaris
                                                @else
                                                    {{ $item->penerima->bidang->nama_bidang ?? '-' }}
                                                @endif

                                            </small>

                                        </td>

                                        <td>

                                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i') }}

                                        </td>

                                        <td>

                                            {{ $item->catatan }}

                                        </td>

                                        <td>

                                            @if ($item->status == 'Menunggu Konfirmasi')
                                                <span class="badge bg-warning text-dark">

                                                    Menunggu Konfirmasi

                                                </span>
                                            @elseif ($item->status == 'ACC')
                                                <span class="badge bg-success">

                                                    ACC

                                                </span>
                                            @elseif ($item->status == 'Ditolak')
                                                <span class="badge bg-danger">

                                                    Ditolak

                                                </span>
                                            @else
                                                <span class="badge bg-secondary">

                                                    {{ $item->status }}

                                                </span>
                                            @endif

                                        </td>

                                        <td>



                                            <button class="btn btn-dark btn-sm" style="width:100px;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#pantauModal{{ $item->id_disposisi }}">

                                                Detail

                                            </button>



                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="5">

                                            Belum ada disposisi.

                                        </td>

                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>



            </div>

            <!-- KANAN : RINGKASAN AGENDA -->
            <x-card-agenda :ringkasanAgenda="$ringkasanAgenda" />

        </div>
    </div>
    @foreach ($laporan as $item)
        <div class="modal fade" id="pantauModal{{ $item->id_disposisi }}" tabindex="-1" aria-hidden="true">

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content border-0 shadow rounded-4">

                    <div class="modal-header">

                        <h5 class="modal-title">

                            Pantau Disposisi Surat

                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body p-4">

                        {{-- HEADER --}}
                        <div class="d-flex justify-content-between align-items-start mb-4">

                            <div>

                                <h4 class="mb-1 fw-semibold">

                                    {{ $item->surat->perihal }}

                                </h4>

                                <small class="text-muted">

                                    {{ $item->surat->nomor_surat }}

                                </small>

                            </div>

                            @if ($item->status == 'Menunggu Konfirmasi')
                                <span class="badge bg-warning text-dark px-3 py-2">

                                    Dalam Proses

                                </span>
                            @elseif ($item->status == 'ACC')
                                <span class="badge bg-success px-3 py-2">

                                    ACC

                                </span>
                            @else
                                <span class="badge bg-danger px-3 py-2">

                                    Ditolak

                                </span>
                            @endif

                        </div>

                        <hr>

                        {{-- INFORMASI SURAT --}}
                        <div class="mb-4">

                            <div class="text-uppercase text-secondary small fw-semibold mb-3">

                                Informasi Surat

                            </div>

                            <div class="row g-4">

                                <div class="col-md-6">

                                    <div class="text-secondary small">

                                        Pengirim

                                    </div>

                                    <div>

                                        {{ $item->surat->asal_surat }}

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="text-secondary small">

                                        Tanggal Surat

                                    </div>

                                    <div>

                                        {{ \Carbon\Carbon::parse($item->surat->tanggal_surat)->format('d M Y') }}

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="text-secondary small">

                                        Nomor Surat

                                    </div>

                                    <div>

                                        {{ $item->surat->nomor_surat }}

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="text-secondary small">

                                        Prioritas

                                    </div>

                                    <div>

                                        @if ($item->surat->prioritas == 'Tinggi')
                                            <span class="badge bg-danger">

                                                Tinggi

                                            </span>
                                        @elseif ($item->surat->prioritas == 'Sedang')
                                            <span class="badge bg-warning text-dark">

                                                Sedang

                                            </span>
                                        @else
                                            <span class="badge bg-success">

                                                Rendah

                                            </span>
                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>

                        <hr>

                        {{-- DISPOSISI KE --}}
                        <div class="mb-4">

                            <div class="text-uppercase text-secondary small fw-semibold mb-3">

                                Disposisikan Kepada

                            </div>

                            <div class="border rounded-3 p-3 bg-body-tertiary">

                                <div class="fw-semibold">

                                    {{ $item->penerima->nama }}

                                </div>

                                <small class="text-muted">

                                    @if ($item->penerima->id_jabatan == 'J006')
                                        Sekretaris
                                    @else
                                        {{ $item->penerima->bidang->nama_bidang ?? '-' }}
                                    @endif

                                </small>

                            </div>

                        </div>

                        <hr>

                        {{-- CATATAN --}}
                        <div class="mb-4">

                            <div class="text-uppercase text-secondary small fw-semibold mb-3">

                                Catatan

                            </div>

                            <div class="border rounded-3 p-3 bg-body-tertiary">

                                {{ $item->catatan }}

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <div class="d-flex justify-content-between w-100">

                            {{-- BATALKAN --}}
                            <form action="{{ route('kepala.disposisi.batal', $item->id_disposisi) }}" method="POST">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Yakin ingin membatalkan disposisi ini?')">

                                    <i class="bi bi-x-circle me-1"></i>

                                    Batalkan Disposisi

                                </button>

                            </form>

                            {{-- LIHAT FILE --}}
                            <a href="{{ asset('storage/surat/' . $item->surat->file_scan) }}" target="_blank"
                                class="btn btn-primary">

                                <i class="bi bi-eye me-1"></i>

                                Lihat File Surat

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    @endforeach
</x-layout>
