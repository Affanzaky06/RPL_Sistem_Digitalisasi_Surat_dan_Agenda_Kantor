<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>

    @php
        $laporan = [
            ['tanggal' => '10/10/2020', 'perihal' => 'Rapat Dinas', 'nama' => 'Owi', 'jabatan' => 'Kabid', 'catatan' => 'Malas', 'status' => 'ACC', 'tone' => 'success', 'aksi' => 'Lihat'],
            ['tanggal' => '10/10/2024', 'perihal' => 'Rapat DPR', 'nama' => 'Wowo', 'jabatan' => 'Kabid', 'catatan' => 'HEII', 'status' => 'Ditolak', 'tone' => 'danger', 'aksi' => 'DispoUlang'],
            ['tanggal' => '10/10/2020', 'perihal' => 'Rapat Dinas', 'nama' => 'Fufufafa', 'jabatan' => 'Sekretaris', 'catatan' => 'Malas', 'status' => 'ACC', 'tone' => 'success', 'aksi' => 'Lihat'],
            ['tanggal' => '10/10/2024', 'perihal' => 'Rapat DPR', 'nama' => 'Bunted', 'jabatan' => 'Subkor', 'catatan' => 'HEII', 'status' => 'Dalam Proses', 'tone' => 'primary', 'aksi' => 'Lihat'],
        ];

        $ringkasanAgenda = [
            ['judul' => 'Meeting 1:', 'nomor' => 'AG-20231015-001', 'peserta' => 'Budi W. (Keuangan), Susi A. (HR), Andi R. (Operasional)'],
            ['judul' => 'Meeting 2:', 'nomor' => 'AG-20231015-001', 'peserta' => 'Budi W. (Keuangan), Susi A. (HR), Andi R. (Operasional)'],
        ];
    @endphp

    <div class="laporan-page">
        <section class="laporan-main">
            <h1>Laporan dan Pemantauan Surat</h1>

            <div class="table-responsive">
                <table class="table laporan-table align-middle">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Perihal</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Catatan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($laporan as $item)
                            <tr>
                                <td>{{ $item['tanggal'] }}</td>
                                <td>{{ $item['perihal'] }}</td>
                                <td>{{ $item['nama'] }}</td>
                                <td>{{ $item['jabatan'] }}</td>
                                <td>{{ $item['catatan'] }}</td>
                                <td>
                                    <span class="laporan-status laporan-status-{{ $item['tone'] }}">{{ $item['status'] }}</span>
                                </td>
                                <td><a href="#">{{ $item['aksi'] }}</a></td>
                            </tr>
                        @endforeach

                        @for ($i = 0; $i < 6; $i++)
                            <tr class="laporan-empty-row">
                                <td colspan="7"></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <nav aria-label="Navigasi halaman laporan" class="d-flex justify-content-end mt-4">
                <ul class="pagination pagination-sm mb-0 disposisi-pagination">
                    <li class="page-item"><a class="page-link" href="#">&lt;</a></li>
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

        <aside class="laporan-summary">
            <h2>Ringkasan Agenda dan Peserta</h2>

            <div class="d-grid gap-3">
                @foreach ($ringkasanAgenda as $item)
                    <article class="agenda-summary-card">
                        <strong>{{ $item['judul'] }}</strong>
                        <p class="mb-1">{{ $item['nomor'] }}</p>
                        <p class="mb-0">Peserta: {{ $item['peserta'] }}</p>
                    </article>
                @endforeach
            </div>
        </aside>
    </div>
</x-layout>
