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
                'detail' => [
                    'perihal' => 'Rapat',
                    'nomor_surat' => '123-45-9',
                    'tanggal_surat' => '10/10/2010',
                    'tanggal_kegiatan' => '10/10/2010',
                    'lokasi' => 'Jl. MBUH no.2 Kantor Kominfo Lt.2',
                    'waktu_mulai' => '09:30',
                    'waktu_selesai' => '20:30',
                    'asal_surat' => 'Dinas Komunikasi dan Informasi',
                    'berkas_scan' => 'surat-rapat-kemendagri.pdf',
                ],
            ],
            [
                'pengirim' => 'Kemendagri',
                'nomor' => '12-3-456',
                'perihal' => 'Rapat koordinasi pegawai nasional',
                'tanggal' => '10-12-2025',
                'prioritas' => 'Sedang',
                'badge' => 'secondary',
                'detail' => [
                    'perihal' => 'Rapat',
                    'nomor_surat' => '123-46-0',
                    'tanggal_surat' => '10/10/2010',
                    'tanggal_kegiatan' => '10/10/2010',
                    'lokasi' => 'Ruang Rapat Kepala Kantor Lt.1',
                    'waktu_mulai' => '10:00',
                    'waktu_selesai' => '12:00',
                    'asal_surat' => 'Kementerian Dalam Negeri',
                    'berkas_scan' => 'surat-koordinasi-pegawai.pdf',
                ],
            ],
            [
                'pengirim' => 'Kemendagri',
                'nomor' => '12-3-456',
                'perihal' => 'Rapat koordinasi pegawai nasional',
                'tanggal' => '10-12-2025',
                'prioritas' => 'Rendah',
                'badge' => 'success',
                'detail' => [
                    'perihal' => 'Rapat',
                    'nomor_surat' => '123-47-1',
                    'tanggal_surat' => '10/10/2010',
                    'tanggal_kegiatan' => '10/10/2010',
                    'lokasi' => 'Aula Kantor Kominfo',
                    'waktu_mulai' => '13:00',
                    'waktu_selesai' => '15:00',
                    'asal_surat' => 'Sekretariat Daerah',
                    'berkas_scan' => 'surat-undangan-rapat.pdf',
                ],
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
                                    <button type="button" class="btn btn-link p-0 disposisi-detail"
                                        data-bs-toggle="modal" data-bs-target="#detailSuratModal"
                                        data-surat='@json($surat['detail'])'>
                                        Detail
                                    </button>
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

    <div class="modal fade surat-detail-modal" id="detailSuratModal" tabindex="-1" aria-labelledby="detailSuratModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="detailSuratModalLabel">Detail Surat</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <form class="surat-detail-form">
                        <label>
                            <span>Perihal</span>
                            <input type="text" class="form-control" data-surat-field="perihal" readonly>
                        </label>

                        <label>
                            <span>Nomor Surat</span>
                            <input type="text" class="form-control" data-surat-field="nomor_surat" readonly>
                        </label>

                        <label>
                            <span>Tanggal Surat</span>
                            <span class="surat-detail-date">
                                <input type="text" class="form-control" data-surat-field="tanggal_surat" readonly>
                                <i class="bi bi-calendar-event-fill"></i>
                            </span>
                        </label>

                        <label>
                            <span>Tanggal Kegiatan</span>
                            <span class="surat-detail-date">
                                <input type="text" class="form-control" data-surat-field="tanggal_kegiatan" readonly>
                                <i class="bi bi-calendar-event-fill"></i>
                            </span>
                        </label>

                        <label class="surat-detail-wide">
                            <span>Lokasi</span>
                            <input type="text" class="form-control" data-surat-field="lokasi" readonly>
                        </label>

                        <label>
                            <span>Waktu Mulai</span>
                            <input type="text" class="form-control" data-surat-field="waktu_mulai" readonly>
                        </label>

                        <label>
                            <span>Waktu Selesai</span>
                            <input type="text" class="form-control" data-surat-field="waktu_selesai" readonly>
                        </label>

                        <label class="surat-detail-wide">
                            <span>Asal Surat</span>
                            <input type="text" class="form-control" data-surat-field="asal_surat" readonly>
                        </label>

                        <div class="surat-detail-wide">
                            <span class="surat-detail-label">Berkas Scan Surat</span>
                            <div class="surat-detail-file">
                                <i class="bi bi-file-earmark-text"></i>
                                <button type="button" class="btn btn-light" data-surat-field="berkas_scan">Buka File</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
