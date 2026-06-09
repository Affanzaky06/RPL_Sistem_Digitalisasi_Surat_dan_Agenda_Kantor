<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>

    @php
        $suratMasuk = [
            [
                'pengirim' => 'Kemendagri',
                'nomor' => '12-3-456',
                'perihal' => 'Rapat koordinasi pegawai nasional',
                'tanggal' => '10-12-2025',
                'prioritas' => 'Urgent',
                'badge' => 'danger',
            ],
            [
                'pengirim' => 'Kemendagri',
                'nomor' => '12-3-456',
                'perihal' => 'Rapat koordinasi pegawai nasional',
                'tanggal' => '10-12-2025',
                'prioritas' => 'Sedang',
                'badge' => 'secondary',
            ],
            [
                'pengirim' => 'Kemendagri',
                'nomor' => '12-3-456',
                'perihal' => 'Rapat koordinasi pegawai nasional',
                'tanggal' => '10-12-2025',
                'prioritas' => 'Rendah',
                'badge' => 'success',
            ],
        ];

        $agenda = [
            [
                'judul' => 'Meeting 1:',
                'nomor' => 'AG-20231015-001',
                'peserta' => 'Budi W. (Keuangan), Susi A. (HR), Andi R. (Operasional)',
            ],
            [
                'judul' => 'Meeting 2:',
                'nomor' => 'AG-20231015-001',
                'peserta' => 'Budi W. (Keuangan), Susi A. (HR), Andi R. (Operasional)',
            ],
        ];
    @endphp

    <div class="disposisi-page">
        <section class="disposisi-main">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <h1 class="disposisi-title mb-0">Disposisi & Keputusan Surat</h1>

                <div class="disposisi-toolbar">
                    <input type="text" class="form-control disposisi-search-input" aria-label="Kata kunci surat">

                    <button type="button" class="btn btn-outline-dark disposisi-search-button">
                        <i class="bi bi-search"></i>
                        <span>Cari</span>
                    </button>

                    <label class="disposisi-sort">
                        <span>SORT BY</span>
                        <select class="form-select">
                            <option selected>Default</option>
                            <option>Terbaru</option>
                            <option>Terlama</option>
                        </select>
                    </label>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table disposisi-table align-middle">
                    <thead>
                        <tr>
                            <th>Pengirim</th>
                            <th>Nomor</th>
                            <th>Perihal</th>
                            <th>Tanggal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suratMasuk as $surat)
                            <tr>
                                <td>{{ $surat['pengirim'] }}</td>
                                <td>{{ $surat['nomor'] }}</td>
                                <td>{{ $surat['perihal'] }}</td>
                                <td>{{ $surat['tanggal'] }}</td>
                                <td class="text-center">
                                    <span class="badge disposisi-priority disposisi-priority-{{ $surat['badge'] }}">
                                        {{ $surat['prioritas'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="disposisi-detail">Detail</a>
                                </td>
                            </tr>
                            <tr class="disposisi-actions-row">
                                <td colspan="6">
                                    <div class="disposisi-actions">
                                        <button type="button" class="btn btn-primary">Disposisi</button>
                                        <button type="button" class="btn btn-success">Hadir</button>
                                        <button type="button" class="btn btn-danger">Tolak</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <nav aria-label="Navigasi halaman disposisi" class="d-flex justify-content-end mt-3">
                <ul class="pagination pagination-sm mb-0 disposisi-pagination">
                    <li class="page-item disabled"><span class="page-link">&lt;</span></li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">...</a></li>
                    <li class="page-item"><a class="page-link" href="#">4</a></li>
                    <li class="page-item"><a class="page-link" href="#">5</a></li>
                    <li class="page-item active"><a class="page-link" href="#">6</a></li>
                    <li class="page-item"><a class="page-link" href="#">7</a></li>
                    <li class="page-item"><a class="page-link" href="#">8</a></li>
                    <li class="page-item"><a class="page-link" href="#">...</a></li>
                    <li class="page-item"><a class="page-link" href="#">20</a></li>
                    <li class="page-item"><a class="page-link" href="#">&gt;</a></li>
                </ul>
            </nav>
        </section>

        <aside class="disposisi-summary">
            <h2>Ringkasan Agenda dan Peserta</h2>

            <div class="d-grid gap-3">
                @foreach ($agenda as $item)
                    <article class="disposisi-summary-card">
                        <strong>{{ $item['judul'] }}</strong>
                        <p class="mb-1">{{ $item['nomor'] }}</p>
                        <p class="mb-0">Peserta: {{ $item['peserta'] }}</p>
                    </article>
                @endforeach
            </div>
        </aside>
    </div>
</x-layout>
