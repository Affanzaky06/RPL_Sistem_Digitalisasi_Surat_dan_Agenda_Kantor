<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="container-fluid p-0">
        <div class="row">

            <div class="col-lg-9 pe-lg-4">

                <h3 class="fw-bold mb-3 fs-4">Riwayat Input Surat</h3>

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <form class="d-flex gap-2" style="width: 100%; max-width: 550px;">
                        <input type="text" class="form-control border-dark border-1 rounded-2">
                        <button type="submit"
                            class="btn bg-white border-dark border-1 rounded-2 d-flex align-items-center gap-2 px-4 text-dark text-nowrap">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </form>

                    <div class="d-flex align-items-center">
                        <small class="fw-medium fs-6 me-2 text-dark">SORT BY</small>
                        <select class="form-select border-dark rounded-2 py-1" style="width:110px;">
                            <option>Default</option>
                            <option>Terbaru</option>
                            <option>Terlama</option>
                        </select>
                    </div>

                </div>

                @php
                    $suratMasuk = [
                        [
                            'pengirim' => 'Kemendagri',
                            'nomor' => '12-3-456',
                            'perihal' => 'Rapat Koordinasi Pegawai Nasional',
                            'tanggal' => '11-12-2025',
                        ],
                        [
                            'pengirim' => 'Kemendagri',
                            'nomor' => '12-3-456',
                            'perihal' => 'Rapat Koordinasi Pegawai Nasional',
                            'tanggal' => '10-12-2025',
                        ],
                        [
                            'pengirim' => 'Kemendagri',
                            'nomor' => '12-3-456',
                            'perihal' => 'Rapat Koordinasi Pegawai Nasional',
                            'tanggal' => '10-12-2025',
                        ],
                        [
                            'pengirim' => 'Kemendagri',
                            'nomor' => '12-3-456',
                            'perihal' => 'Rapat Koordinasi Pegawai Nasional',
                            'tanggal' => '10-12-2025',
                        ],
                    ];
                @endphp

                <div class="border border-dark rounded-3 overflow-hidden mb-3 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle text-center mb-0"
                            style="border-style: hidden;">
                            <thead style="background-color: #e9ecef; border-bottom: 1px solid #212529;" class="fs-5 ">
                                <tr>
                                    <th scope="col" class="py-2 fw-medium text-dark">Pengirim</th>
                                    <th scope="col" class="py-2 fw-medium text-dark">Nomor</th>
                                    <th scope="col" class="py-2 fw-medium text-dark">Perihal</th>
                                    <th scope="col" class="py-2 fw-medium text-dark">Tanggal</th>
                                    <th scope="col" class="py-2 fw-medium text-dark">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="fs-6 bg-white">
                                @foreach ($suratMasuk as $surat)
                                    <tr style="border-bottom: 1px solid #dee2e6;">
                                        <td class="text-dark py-2">{{ $surat['pengirim'] }}</td>
                                        <td class="text-dark py-2">{{ $surat['nomor'] }}</td>
                                        <td class="text-dark py-2" style="max-width: 220px;">{{ $surat['perihal'] }}
                                        </td>
                                        <td class="text-dark py-2">{{ $surat['tanggal'] }}</td>

                                        <td class="py-2" style="width: 120px;">
                                            <div class="d-flex flex-column gap-1 px-2">
                                                <a href="#" class="btn btn-primary btn-sm rounded-1"
                                                    style="font-size: 0.7rem; background-color: #0d6efd;">Lihat
                                                    Detail</a>
                                                <a href="#" class="btn btn-warning btn-sm rounded-1 text-white"
                                                    style="font-size: 0.7rem; background-color: #ffc107;">Edit</a>

                                                <form action="#" method="POST" class="d-inline m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm rounded-1 w-100"
                                                        style="font-size: 0.7rem; background-color: #dc3545;">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-start pagination-sm mb-0">
                        <li class="page-item disabled"><a class="page-link text-dark" href="#">&laquo;</a></li>
                        <li class="page-item"><a class="page-link text-dark" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link text-dark" href="#">...</a></li>
                        <li class="page-item"><a class="page-link text-dark" href="#">4</a></li>
                        <li class="page-item"><a class="page-link text-dark" href="#">5</a></li>
                        <li class="page-item active"><a class="page-link" href="#">6</a></li>
                        <li class="page-item"><a class="page-link text-dark" href="#">7</a></li>
                        <li class="page-item"><a class="page-link text-dark" href="#">8</a></li>
                        <li class="page-item disabled"><a class="page-link text-dark" href="#">...</a></li>
                        <li class="page-item"><a class="page-link text-dark" href="#">20</a></li>
                        <li class="page-item"><a class="page-link text-dark" href="#">&raquo;</a></li>
                    </ul>
                </nav>

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
</x-layout>
