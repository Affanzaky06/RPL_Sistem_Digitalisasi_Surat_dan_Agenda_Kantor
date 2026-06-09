<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>

    @php
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $jam = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];
        $user = auth()->user();
        $agendaOwner = $user->nama ?? $role;
        $agendaKey = $user->nip ?? $role;

        $agendaPerUser = [
            $agendaKey => [
                [
                    'date' => '2024-12-02',
                    'start' => '09:30',
                    'end' => '13:00',
                    'title' => 'Rapat Koordinasi MBG',
                    'place' => 'Jl. Jend Sudirman, Jakarta, Kantor MBG, Ruang Rapat Kemendagri, Lt. 5',
                    'status' => 'Terlaksana',
                    'tone' => 'warning',
                    'participants' => 'Budi W. (Keuangan), Susi A. (HR), Andi R. (Operasional)',
                ],
                [
                    'date' => '2024-12-03',
                    'start' => '09:30',
                    'end' => '13:30',
                    'title' => 'Rapat Koordinasi MBG',
                    'place' => 'Jl. Jend Sudirman, Jakarta, Kantor MBG, Ruang Rapat Kemendagri, Lt. 5',
                    'status' => 'Sedang Berlangsung',
                    'tone' => 'success',
                    'participants' => 'Budi W. (Keuangan), Susi A. (HR), Andi R. (Operasional)',
                ],
                [
                    'date' => '2024-12-04',
                    'start' => '13:30',
                    'end' => '17:30',
                    'title' => 'Rapat Koordinasi Kominfo',
                    'place' => 'Jl. Jend Sudirman, Jakarta, Kantor MBG, Ruang Rapat Kemendagri, Lt. 5',
                    'status' => 'Akan Datang',
                    'tone' => 'primary',
                    'participants' => 'Budi W. (Keuangan), Susi A. (HR), Andi R. (Operasional)',
                ],
                [
                    'date' => '2024-12-05',
                    'start' => '09:30',
                    'end' => '15:30',
                    'title' => 'Rapat Koordinasi Kemendagri',
                    'place' => 'Jl. Jend Sudirman, Jakarta, Kantor MBG, Ruang Rapat Kemendagri, Lt. 5',
                    'status' => 'Akan Datang',
                    'tone' => 'primary',
                    'participants' => 'Sekretaris, Kabid Keuangan, Kabid Operasional',
                    'note' => 'Membahas Kenaikan Gaji',
                ],
            ],
        ];

        $agendaItems = $agendaPerUser[$agendaKey] ?? [];
    @endphp

    <div class="agenda-page" data-agenda-calendar data-owner="{{ $agendaOwner }}"
        data-events='@json($agendaItems)'>
        <section class="agenda-calendar" aria-label="Kalender agenda mingguan">
            <div class="agenda-calendar-header">
                <div class="agenda-week-control">
                    <button type="button" class="btn btn-link" aria-label="Periode sebelumnya" data-agenda-prev>
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <button type="button" class="btn btn-link" aria-label="Periode berikutnya" data-agenda-next>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                    <h1 data-agenda-title>Minggu 1 - Desember 2024</h1>
                </div>

                <div class="btn-group agenda-view-switch" role="group" aria-label="Pilihan tampilan agenda">
                    <button type="button" class="btn btn-outline-secondary" data-agenda-view="day">Hari</button>
                    <button type="button" class="btn btn-outline-dark active" data-agenda-view="week">Minggu</button>
                    <button type="button" class="btn btn-outline-secondary" data-agenda-view="month">Bulan</button>
                </div>
            </div>

            <div data-agenda-content></div>
            <div class="agenda-popover d-none" data-agenda-popover></div>
        </section>

        <aside class="agenda-summary">
            <h2>Ringkasan Agenda dan Peserta</h2>

            <div class="d-grid gap-3" data-agenda-summary></div>
        </aside>
    </div>
</x-layout>
