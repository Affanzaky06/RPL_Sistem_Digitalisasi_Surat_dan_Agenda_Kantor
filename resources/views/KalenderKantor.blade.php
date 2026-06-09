<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>

    @php
        $agendaKantor = [
            [
                'id' => 'kantor-001',
                'date' => '2026-12-01',
                'start' => '10:30',
                'end' => '14:30',
                'title' => 'Rapat Koordinasi MBG',
                'place' => 'Jl. Jend Sudirman, Jakarta. Kantor MBG, Ruang Rapat Kemendagri, Lt. 5',
                'status' => 'Terlaksana',
                'tone' => 'warning',
                'owner' => 'Kepala Kantor',
                'group' => 'kepala-kantor',
                'participants' => 'Kepala Kantor, Sekretaris, Kabid Pembangunan',
            ],
            [
                'id' => 'kantor-002',
                'date' => '2026-12-02',
                'start' => '09:30',
                'end' => '13:00',
                'title' => 'Rapat Koordinasi MBG',
                'place' => 'Jl. Jend Sudirman, Jakarta. Kantor MBG, Ruang Rapat Kemendagri, Lt. 5',
                'status' => 'Sedang Berlangsung',
                'tone' => 'success',
                'owner' => 'Kabid Pembangunan',
                'group' => 'kabid-pembangunan',
                'participants' => 'Kabid Pembangunan, Subkoor Infrastruktur',
            ],
            [
                'id' => 'kantor-003',
                'date' => '2026-12-03',
                'start' => '14:30',
                'end' => '18:30',
                'title' => 'Rapat Koordinasi Kominfo',
                'place' => 'Jl. Jend Sudirman, Jakarta. Kantor MBG, Ruang Rapat Kemendagri, Lt. 5',
                'status' => 'Akan Datang',
                'tone' => 'primary',
                'owner' => 'Kabid Operasional',
                'group' => 'kabid-operasional',
                'participants' => 'Kabid Operasional, Staff Operasional',
            ],
            [
                'id' => 'kantor-004',
                'date' => '2026-12-04',
                'start' => '10:30',
                'end' => '17:00',
                'title' => 'Rapat Koordinasi Kemendagri',
                'place' => 'Jl. Jend Sudirman, Jakarta. Kantor MBG, Ruang Rapat Kemendagri, Lt. 5',
                'status' => 'Akan Datang',
                'tone' => 'primary',
                'owner' => 'Sekretaris',
                'group' => 'sekretaris',
                'participants' => 'Sekretaris, Kepala Kantor',
            ],
            [
                'id' => 'kantor-005',
                'date' => '2026-12-05',
                'start' => '09:00',
                'end' => '12:00',
                'title' => 'Sinkronisasi Anggaran',
                'place' => 'Ruang Rapat Keuangan',
                'status' => 'Akan Datang',
                'tone' => 'primary',
                'owner' => 'Kabid Keuangan',
                'group' => 'kabid-keuangan',
                'participants' => 'Kabid Keuangan, Bendahara, Staff Keuangan',
            ],
            ['id' => 'blocked-001', 'date' => '2026-12-06', 'start' => '16:00', 'end' => '19:00', 'title' => '', 'place' => '', 'status' => '', 'tone' => 'blocked', 'group' => 'all'],
            ['id' => 'blocked-002', 'date' => '2026-12-07', 'start' => '12:00', 'end' => '15:00', 'title' => '', 'place' => '', 'status' => '', 'tone' => 'blocked', 'group' => 'all'],
        ];
    @endphp

    <div class="agenda-office-page" data-agenda-calendar data-owner="Semua Sivitas Kantor"
        data-events='@json($agendaKantor)'>
        <section class="agenda-calendar agenda-calendar-office" aria-label="Kalender kantor mingguan">
            <div class="agenda-calendar-header">
                <div class="agenda-week-control">
                    <button type="button" class="btn btn-link" aria-label="Periode sebelumnya" data-agenda-prev>
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <button type="button" class="btn btn-link" aria-label="Periode berikutnya" data-agenda-next>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                    <h1 data-agenda-title>Minggu - 1 Desember 2026</h1>
                </div>

                <div class="agenda-office-actions">
                    <div class="btn-group agenda-view-switch" role="group" aria-label="Pilihan tampilan kalender kantor">
                        <button type="button" class="btn btn-outline-secondary" data-agenda-view="day">Hari</button>
                        <button type="button" class="btn btn-outline-dark active" data-agenda-view="week">Minggu</button>
                        <button type="button" class="btn btn-outline-secondary" data-agenda-view="month">Bulan</button>
                    </div>

                    <label class="agenda-office-filter">
                        <select class="form-select" aria-label="Pilih agenda kantor" data-agenda-filter>
                            <option value="all">Semua Sivitas Kantor</option>
                            <option value="kepala-kantor">Kepala Kantor</option>
                            <option value="kabid-pembangunan">Kabid Pembangunan</option>
                            <option value="kabid-keuangan">Kabid Keuangan</option>
                            <option value="kabid-operasional">Kabid Operasional</option>
                            <option value="sekretaris">Sekretaris</option>
                        </select>
                        <i class="bi bi-search"></i>
                        <i class="bi bi-chevron-down"></i>
                    </label>
                </div>
            </div>

            <div data-agenda-content></div>
        </section>
    </div>
</x-layout>
