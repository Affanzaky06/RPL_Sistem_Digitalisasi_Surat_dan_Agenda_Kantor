<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container-fluid">
        <div class="row">
            <!-- KIRI : TABEL -->
            <div class="col-lg-9">
                @if (session('success'))
                    <div class="position-fixed top-0 end-0 p-3" style="z-index:9999;">
                        <div class="toast show border-0 shadow">
                            <div class="toast-body">
                                <span class="text-success"><i class="bi bi-check-circle-fill me-2"></i></span>
                                {{ session('success') }}
                            </div>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="position-fixed top-0 end-0 p-3" style="z-index:9999;">
                        <div class="toast show border-0 text-bg-danger shadow">
                            <div class="toast-body">
                                <span class="text-white"><i class="bi bi-exclamation-circle-fill me-2"></i></span>
                                {{ session('error') }}
                            </div>
                        </div>
                    </div>
                @endif
                <script>
                    setTimeout(() => {
                        document.querySelectorAll('.toast').forEach(el => el.remove());
                    }, 6000);
                </script>

                <h3 class="fw-bold mb-4">Laporan dan Pemantauan Surat</h3>

                <form method="GET" class="mb-4">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                        <div class="input-group" style="max-width: 450px;">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari pengirim, perihal, atau catatan..." value="{{ request('search') }}">
                            <button class="btn btn-outline-dark" type="submit">
                                <i class="bi bi-search me-1"></i>
                                Cari
                            </button>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="text-uppercase fw-semibold" style="font-size:12px; color:#555;">
                                Sort By
                            </span>
                            <select name="sort" class="form-select" style="width:140px;"
                                onchange="this.form.submit()">
                                <option value="terbaru" @selected(request('sort', 'terbaru') === 'terbaru')>
                                    Terbaru
                                </option>
                                <option value="terlama" @selected(request('sort') === 'terlama')>
                                    Terlama
                                </option>
                            </select>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <!-- Tabel disesuaikan dengan desain mockup (tanpa border luar, garis hitam tegas di header) -->
                    <table class="table table-borderless align-middle text-center mb-0"
                        style="border-top: 2px solid #333; border-bottom: 2px solid #333;">
                        <thead>
                            <tr style="border-bottom: 2px solid #333;">
                                <th class="fw-semibold py-3 fs-6">Tanggal</th>
                                <th class="fw-semibold py-3 fs-6">Perihal</th>
                                <th class="fw-semibold py-3 fs-6">Nama</th>
                                <th class="fw-semibold py-3 fs-6">Jabatan</th>
                                <th class="fw-semibold py-3 fs-6">Catatan</th>
                                <th class="fw-semibold py-3 fs-6">Status</th>
                                <th class="fw-semibold py-3 fs-6">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="fs-6 bg-white">
                            @forelse ($laporan as $item)
                                <tr style="border-bottom: 1px solid #ccc;">
                                    <td class="py-3">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                    </td>

                                    <td class="py-3 text-start">
                                        {{ \Illuminate\Support\Str::limit($item->surat->perihal ?? '-', 25) }}
                                    </td>

                                    <td class="py-3">
                                        {{ $item->penerima->nama ?? '-' }}
                                    </td>

                                    <td class="py-3">
                                        @switch($item->penerima->id_jabatan ?? '')
                                            @case('J001')
                                                Kepala
                                            @break

                                            @case('J002')
                                                Kabid
                                            @break

                                            @case('J003')
                                                Subkoor
                                            @break

                                            @case('J004')
                                                Staff
                                            @break

                                            @case('J006')
                                                Sekretaris
                                            @break

                                            @default
                                                -
                                        @endswitch
                                    </td>

                                    <td class="py-3 text-start">
                                        {{ \Illuminate\Support\Str::limit($item->catatan, 15) }}
                                    </td>

                                    <td class="py-3">
                                        {{-- LOGIKA WARNA STATUS SESUAI MOCKUP --}}
                                        @if (in_array($item->status, ['Hadir', 'Sudah Diproses', 'ACC', 'Didisposisikan']))
                                            <span
                                                class="badge bg-success-subtle text-success border border-success px-3 py-2"
                                                style="width: 100px;">ACC</span>
                                        @elseif(in_array($item->status, ['Tidak Hadir', 'Ditolak', 'Dibatalkan']))
                                            <span
                                                class="badge bg-danger-subtle text-danger border border-danger px-3 py-2"
                                                style="width: 100px;">Ditolak</span>
                                        @elseif(in_array($item->status, ['Digantikan', 'Dimaklumi', 'Selesai', 'Tidak Tertangani']))
                                            <span
                                                class="badge bg-secondary-subtle text-secondary border border-secondary px-3 py-2"
                                                style="width: 100px;">Selesai</span>
                                        @else
                                            <span
                                                class="badge bg-primary-subtle text-primary border border-primary px-3 py-2"
                                                style="width: 120px;">Dalam Proses</span>
                                        @endif
                                    </td>

                                    <td class="py-3">
                                        {{-- LOGIKA TOMBOL AKSI --}}
                                        @if (in_array($item->status, ['Tidak Hadir', 'Ditolak']))
                                            @php
                                                $isAttending = \App\Models\Peserta::whereHas('agenda', function (
                                                    $q,
                                                ) use ($item) {
                                                    $q->where('id_surat', $item->id_surat);
                                                })
                                                    ->where('nip', auth()->user()->nip)
                                                    ->exists();
                                            @endphp
                                            <div class="d-flex flex-column gap-1 align-items-center">
                                                <a href="javascript:void(0)"
                                                    class="text-primary text-decoration-none fw-medium text-center"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#dispoUlangModal{{ $item->id_disposisi }}">
                                                    @if ($isAttending)
                                                        Ganti Pendamping
                                                    @else
                                                        Dispo Ulang
                                                    @endif
                                                </a>

                                                @if ($isAttending)
                                                    {{-- Jika user sudah hadir (ini adalah penolakan pendamping) --}}
                                                    <form action="{{ route('laporan.setujui', $item->id_disposisi) }}"
                                                        method="POST" id="formSetujui{{ $item->id_disposisi }}">
                                                        @csrf
                                                        <button type="button"
                                                            class="btn btn-link text-success text-decoration-none p-0 fw-medium"
                                                            style="font-size: 0.85rem;" data-bs-toggle="modal"
                                                            data-bs-target="#setujuModal{{ $item->id_disposisi }}">Setujui</button>
                                                    </form>
                                                @else
                                                    {{-- Jika user belum hadir (ini adalah penolakan disposisi biasa) --}}
                                                    <a href="javascript:void(0)"
                                                        class="text-success text-decoration-none fw-medium"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#hadirModal{{ $item->id_disposisi }}">Hadir</a>

                                                    @if (auth()->user()->id_jabatan === 'J002' || auth()->user()->id_jabatan === 'J003')
                                                        <a href="javascript:void(0)"
                                                            class="text-danger text-decoration-none fw-medium"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#tolakModal{{ $item->id_disposisi }}">Tolak</a>
                                                    @endif
                                                @endif
                                            </div>
                                        @else
                                            <a href="javascript:void(0)"
                                                class="text-primary text-decoration-none fw-medium"
                                                data-bs-toggle="modal"
                                                data-bs-target="#pantauModal{{ $item->id_disposisi }}">Lihat</a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-4 text-muted">Belum ada riwayat disposisi atau ajakan
                                            pendamping.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $laporan->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
                <!-- KANAN : RINGKASAN AGENDA -->
                <x-card-agenda :ringkasanAgenda="$ringkasanAgenda" />
            </div>
        </div>





        <!-- SEMUA MODAL DILOOP DI BAWAH SINI -->
        @foreach ($laporan as $item)
            <!-- MODAL PANTAU (DETAIL BIASA BAWAAN ANDA) -->
            <div class="modal fade" id="pantauModal{{ $item->id_disposisi }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4">
                        <div class="modal-header">
                            <h5 class="modal-title">Pantau Disposisi Surat</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="border rounded-3 p-3 mb-4 bg-white shadow-sm d-flex position-relative">
                                <div class="col-6 pe-3" style="border-right: 2px dashed #dee2e6;">
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1"><i
                                                class="bi bi-send me-2"></i>Pengirim</small>
                                        <span class="fw-bold text-dark">{{ $item->surat->asal_surat }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-hash me-2"></i>Nomor
                                            Surat</small>
                                        <span class="fw-bold text-dark">{{ $item->surat->nomor_surat }}</span>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block mb-1"><i
                                                class="bi bi-file-earmark-text me-2"></i>Perihal Surat</small>
                                        <span class="fw-bold text-dark">{{ $item->surat->perihal }}</span>
                                    </div>
                                </div>

                                <div class="col-6 ps-4 position-relative">
                                    <div class="mb-3 d-flex justify-content-between align-items-start">
                                        <div>
                                            <small class="text-muted d-block mb-1"><i
                                                    class="bi bi-calendar me-2"></i>Tanggal Kegiatan</small>
                                            <span
                                                class="fw-bold text-dark">{{ \Carbon\Carbon::parse($item->surat->tanggal_kegiatan)->format('d-m-Y') }}</span>
                                        </div>
                                        @if (in_array($item->status, ['Hadir', 'Sudah Diproses', 'ACC', 'Didisposisikan']))
                                            <span
                                                class="badge bg-success-subtle text-success border border-success px-3 py-1">{{ $item->status }}</span>
                                        @elseif(in_array($item->status, ['Tidak Hadir', 'Ditolak', 'Dibatalkan']))
                                            <span
                                                class="badge bg-danger-subtle text-danger border border-danger px-3 py-1">{{ $item->status }}</span>
                                        @elseif(in_array($item->status, ['Digantikan', 'Dimaklumi', 'Selesai', 'Tidak Tertangani']))
                                            <span
                                                class="badge bg-secondary-subtle text-secondary border border-secondary px-3 py-1">{{ $item->status }}</span>
                                        @else
                                            <span
                                                class="badge bg-primary-subtle text-primary border border-primary px-3 py-1">{{ $item->status }}</span>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1"><i
                                                class="bi bi-clock me-2"></i>Waktu</small>
                                        <span class="fw-bold text-dark">
                                            {{ $item->surat->waktu_mulai_kegiatan ? \Carbon\Carbon::parse($item->surat->waktu_mulai_kegiatan)->format('H:i') : '-' }}
                                            -
                                            {{ $item->surat->waktu_selesai_kegiatan ? \Carbon\Carbon::parse($item->surat->waktu_selesai_kegiatan)->format('H:i') : '-' }}
                                        </span>
                                    </div>

                                    <div>
                                        <small class="text-muted d-block mb-1"><i
                                                class="bi bi-info-circle me-2"></i>Prioritas</small>
                                        @if ($item->surat->prioritas == 'Tinggi')
                                            <span class="badge bg-danger px-3 py-1">Tinggi</span>
                                        @elseif($item->surat->prioritas == 'Sedang')
                                            <span class="badge bg-warning text-dark px-3 py-1">Sedang</span>
                                        @else
                                            <span class="badge bg-success px-3 py-1">Rendah</span>
                                        @endif
                                    </div>

                                    <a href="{{ asset('storage/surat/' . $item->surat->file_scan) }}" target="_blank"
                                        class="btn btn-outline-secondary btn-sm position-absolute bottom-0 end-0 me-3"
                                        style="text-decoration:none;">
                                        <i class="bi bi-eye"></i> Lihat Detail
                                    </a>
                                </div>
                            </div>

                            <div class="border rounded-3 p-3 mb-4 bg-white shadow-sm">
                                <label class="fw-bold mb-3 d-flex align-items-center text-dark">
                                    <i class="bi bi-people me-2 fs-5"></i> Didisposisikan kepada
                                </label>

                                <div class="d-flex align-items-center gap-3 ms-2">
                                    <i class="bi bi-person-circle fs-2 text-secondary"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $item->penerima->nama ?? '-' }}</h6>
                                        <small class="text-muted">
                                            @switch($item->penerima->id_jabatan ?? '')
                                                @case('J001')
                                                    Kepala
                                                @break

                                                @case('J002')
                                                    Kabid
                                                @break

                                                @case('J003')
                                                    Subkoor
                                                @break

                                                @case('J004')
                                                    Staff
                                                @break

                                                @case('J006')
                                                    Sekretaris
                                                @break

                                                @default
                                                    -
                                            @endswitch
                                            @if (isset($item->penerima->bidang))
                                                | {{ $item->penerima->bidang->nama_bidang }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded-3 p-3 bg-white shadow-sm mb-2">
                                <label class="fw-bold mb-2 d-flex align-items-center text-dark">
                                    <i class="bi bi-journal-text me-2 fs-5"></i> Catatan
                                </label>
                                <div class="ms-2 mt-1 text-dark">
                                    {{ $item->catatan }}
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0 pb-3">
                            <div class="d-flex justify-content-between w-100">
                                @if (in_array($item->status, ['Menunggu Konfirmasi', 'Belum Dibaca']))
                                    @php
                                        // Deteksi route dinamis berdasarkan role pembatal
                                        $rolePrefix = '';
                                        if (Auth::user()->id_jabatan == 'J001') {
                                            $rolePrefix = 'kepala';
                                        } elseif (Auth::user()->id_jabatan == 'J002') {
                                            $rolePrefix = 'kabid';
                                        } elseif (Auth::user()->id_jabatan == 'J003') {
                                            $rolePrefix = 'subkoor';
                                        }
                                    @endphp
                                    <form action="{{ route($rolePrefix . '.disposisi.batal', $item->id_disposisi) }}"
                                        method="POST" id="formBatalDisposisi{{ $item->id_disposisi }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#batalDisposisiModal{{ $item->id_disposisi }}">
                                            <i class="bi bi-x-circle me-1"></i> Batalkan Disposisi
                                        </button>
                                    </form>
                                @else
                                    <div></div> {{-- Spacer --}}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL DISPOSISI ULANG / GANTI PENDAMPING (KHUSUS JIKA DITOLAK/TIDAK HADIR) -->
            <div class="modal fade" id="dispoUlangModal{{ $item->id_disposisi }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4 p-2">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="fw-bold fs-4">
                                @if (str_contains($item->catatan, 'Pendamping') ||
                                        \App\Models\Peserta::whereHas('agenda', function ($q) use ($item) {
                                            $q->where('id_surat', $item->id_surat);
                                        })->where('nip', auth()->user()->nip)->exists())
                                    Ganti Pendamping
                                @else
                                    Disposisi Ulang Surat
                                @endif
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('laporan.dispo_ulang', $item->id_disposisi) }}" method="POST">
                                @csrf

                                <!-- INFO SURAT -->
                                <div class="border rounded-3 p-3 mb-4 d-flex">
                                    <div class="col-6 border-end pe-3">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-send me-1"></i>
                                            Pengirim</small>
                                        <div class="fw-bold mb-2">{{ $item->surat->asal_surat }}</div>
                                        <small class="text-muted d-block mb-1"><i class="bi bi-hash me-1"></i> Nomor
                                            Surat</small>
                                        <div class="fw-bold mb-2">{{ $item->surat->nomor_surat }}</div>
                                        <small class="text-muted d-block mb-1"><i class="bi bi-file-text me-1"></i>
                                            Perihal Surat</small>
                                        <div class="fw-bold">{{ $item->surat->perihal }}</div>
                                    </div>
                                    <div class="col-6 ps-4 position-relative">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-calendar me-1"></i> Tanggal
                                            Kegiatan</small>
                                        <div class="fw-bold mb-2">
                                            {{ \Carbon\Carbon::parse($item->surat->tanggal_kegiatan)->format('d-m-Y') }}
                                        </div>
                                        <small class="text-muted d-block mb-1"><i class="bi bi-clock me-1"></i>
                                            Waktu</small>
                                        <div class="fw-bold mb-2">{{ $item->surat->waktu_mulai_kegiatan ?? '-' }} -
                                            {{ $item->surat->waktu_selesai_kegiatan ?? '-' }}</div>
                                        <small class="text-muted d-block mb-1"><i class="bi bi-info-circle me-1"></i>
                                            Prioritas</small>
                                        <span class="badge bg-danger">{{ $item->surat->prioritas }}</span>

                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm position-absolute bottom-0 end-0"
                                            data-bs-dismiss="modal" data-bs-toggle="modal"
                                            data-bs-target="#pantauModal{{ $item->id_disposisi }}">
                                            <i class="bi bi-eye"></i> Lihat Detail
                                        </button>
                                    </div>
                                </div>

                                <!-- DAFTAR PEGAWAI BARU -->
                                <!-- DAFTAR PEGAWAI BARU (MODAL DISPO ULANG) -->
                                <div class="border rounded-3 p-3 mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>Pilih yang ingin
                                            diDisposisi</h6>
                                        <div class="d-flex gap-2">
                                            <!-- INPUT SEARCH (Ganti data-target dengan akhiran -ulang) -->
                                            <input type="text" class="form-control form-control-sm search-pendamping"
                                                data-target="list-pendamping-ulang-{{ $item->id_disposisi }}"
                                                placeholder="Cari Nama..." style="width: 150px;">

                                            <select class="form-select form-select-sm sort-jabatan"
                                                data-target="list-pendamping-ulang-{{ $item->id_disposisi }}"
                                                style="width: 140px;">
                                                <option value="">Semua Jabatan</option>
                                                @foreach ($jabatanTersedia as $jabatan)
                                                    <option value="{{ strtolower($jabatan) }}">{{ $jabatan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- KONTAINER LIST (Ganti id dengan akhiran -ulang) -->
                                    <div class="list-group" id="list-pendamping-ulang-{{ $item->id_disposisi }}"
                                        style="max-height: 180px; overflow-y: auto;">
                                        @foreach ($pegawai as $p)
                                            <label
                                                class="list-group-item d-flex gap-3 align-items-center p-3 pendamping-item"
                                                data-nama="{{ strtolower($p->nama) }}"
                                                data-jabatan="{{ strtolower(match ($p->id_jabatan) {'J002' => 'Kabid','J003' => 'Subkoor','J004' => 'Staff','J006' => 'Sekretaris',default => ''}) }}"
                                                style="cursor: pointer;">
                                                <input class="form-check-input flex-shrink-0 fs-5 mt-0" type="radio"
                                                    required name="nip_pendamping" value="{{ $p->nip }}">
                                                <div class="d-flex align-items-center gap-3">
                                                    <i class="bi bi-person-circle fs-3 text-secondary"></i>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $p->nama }}</h6>
                                                        <small class="text-muted">
                                                            {{ match ($p->id_jabatan) {'J002' => 'Kabid','J003' => 'Subkoor','J004' => 'Staff','J006' => 'Sekretaris',default => $p->id_jabatan} }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>




                                <!-- CATATAN -->
                                <div class="border rounded-3 p-3">
                                    <h6 class="fw-bold mb-2"><i class="bi bi-journal-text me-2"></i>Catatan</h6>
                                    <textarea name="catatan" rows="2" class="form-control" placeholder="Tulis Catatan Disini... (Opsional)">{{ $item->catatan }}</textarea>
                                </div>

                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-success px-5 fw-bold"
                                        style="background-color: #198754;">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- MODAL HADIR AMBIL ALIH -->
            <div class="modal fade" id="hadirModal{{ $item->id_disposisi }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4 p-2">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="fw-bold fs-4">Hadir / Ambil Alih</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('laporan.hadir', $item->id_disposisi) }}" method="POST">
                                @csrf
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Dengan memilih <strong>Hadir</strong>, Anda mengambil alih disposisi yang ditolak
                                    bawahan ini. Agenda kehadiran akan otomatis dibuat untuk Anda. Anda juga bisa memilih
                                    pendamping di bawah ini.
                                </div>

                                <!-- DAFTAR PEGAWAI BARU (MODAL HADIR) -->
                                <div class="border rounded-3 p-3 mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>Pilih Pendamping
                                            (Opsional)</h6>
                                        <div class="d-flex gap-2">
                                            <!-- INPUT SEARCH (Ganti data-target dengan akhiran -hadir) -->
                                            <input type="text" class="form-control form-control-sm search-pendamping"
                                                data-target="list-pendamping-hadir-{{ $item->id_disposisi }}"
                                                placeholder="Cari Nama..." style="width: 150px;">

                                            <select class="form-select form-select-sm sort-jabatan"
                                                data-target="list-pendamping-hadir-{{ $item->id_disposisi }}"
                                                style="width: 140px;">
                                                <option value="">Semua Jabatan</option>
                                                @foreach ($jabatanTersedia as $jabatan)
                                                    <option value="{{ strtolower($jabatan) }}">{{ $jabatan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- KONTAINER LIST (Ganti id dengan akhiran -hadir) -->
                                    <div class="list-group" id="list-pendamping-hadir-{{ $item->id_disposisi }}"
                                        style="max-height: 180px; overflow-y: auto;">
                                        @foreach ($pegawai as $p)
                                            <label
                                                class="list-group-item d-flex gap-3 align-items-center p-3 pendamping-item"
                                                data-nama="{{ strtolower($p->nama) }}"
                                                data-jabatan="{{ strtolower(match ($p->id_jabatan) {'J002' => 'Kabid','J003' => 'Subkoor','J004' => 'Staff','J006' => 'Sekretaris',default => ''}) }}"
                                                style="cursor: pointer;">
                                                <input class="form-check-input flex-shrink-0 fs-5 mt-0" type="checkbox"
                                                    name="nip_pendamping[]" value="{{ $p->nip }}">
                                                <div class="d-flex align-items-center gap-3">
                                                    <i class="bi bi-person-circle fs-3 text-secondary"></i>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $p->nama }}</h6>
                                                        <small class="text-muted">
                                                            {{ match ($p->id_jabatan) {'J002' => 'Kabid','J003' => 'Subkoor','J004' => 'Staff','J006' => 'Sekretaris',default => $p->id_jabatan} }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-success px-5 fw-bold">Konfirmasi
                                        Hadir</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL TOLAK KE ATASAN -->
            <div class="modal fade" id="tolakModal{{ $item->id_disposisi }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4 p-2">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="fw-bold fs-4 text-danger">Tolak & Kembalikan ke Atasan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('laporan.tolak', $item->id_disposisi) }}" method="POST">
                                @csrf
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    Surat ini akan dikembalikan ke atasan Anda (Karena bawahan Anda menolak, dan Anda juga
                                    berhalangan).
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Alasan Penolakan</label>
                                    <textarea name="alasan_tolak" class="form-control" rows="3" required
                                        placeholder="Jelaskan kenapa Anda dan bawahan tidak bisa hadir..."></textarea>
                                </div>
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-danger px-5 fw-bold">Tolak Surat</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="setujuModal{{ $item->id_disposisi }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4 p-2">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold fs-4">Konfirmasi Hadir Sendiri</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-3">
                            <p class="fs-6">Maklumi penolakan pendamping ini dan hadir sendiri tanpa dia?</p>
                            <p class="text-muted small">Tindakan ini akan mengonfirmasi kehadiran Anda pada agenda ini.</p>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4">
                            <button type="button" class="btn btn-secondary px-4 fw-bold"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" form="formSetujui{{ $item->id_disposisi }}"
                                class="btn btn-success px-4 fw-bold">Setujui</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="batalDisposisiModal{{ $item->id_disposisi }}" tabindex="-1"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4 p-2">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold fs-4 text-danger">Batalkan Disposisi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-3">
                            <p class="fs-6">Yakin ingin membatalkan disposisi ini?</p>
                            <p class="text-danger small"><i class="bi bi-exclamation-triangle-fill me-1"></i> Disposisi
                                akan ditarik kembali dari penerima.</p>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4">
                            <button type="button" class="btn btn-secondary px-4 fw-bold"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" form="formBatalDisposisi{{ $item->id_disposisi }}"
                                class="btn btn-danger px-4 fw-bold">Ya, Batalkan</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach


        <!-- SCRIPT JAVASCRIPT UNTUK FITUR PENCARIAN NAMA -->

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                function filterPendamping(targetId, triggerElement) {
                    const container = document.getElementById(targetId);
                    if (!container) return;

                    // Mencari input dan select yang memiliki data-target yang sama
                    const modalContext = triggerElement.closest('.modal-body');
                    const searchInput = modalContext.querySelector(`.search-pendamping[data-target="${targetId}"]`);
                    const jabatanSelect = modalContext.querySelector(`.sort-jabatan[data-target="${targetId}"]`);

                    const keyword = (searchInput?.value || '').toLowerCase().trim();
                    const jabatan = (jabatanSelect?.value || '').toLowerCase().trim();

                    container.querySelectorAll('.pendamping-item').forEach(item => {
                        const nama = (item.dataset.nama || '').toLowerCase();
                        const itemJabatan = (item.dataset.jabatan || '').toLowerCase();

                        const cocokNama = nama.includes(keyword);
                        const cocokJabatan = !jabatan || itemJabatan === jabatan;

                        if (cocokNama && cocokJabatan) {
                            item.insertAdjacentHTML('afterbegin', ''); // Triggers layout refresh if needed
                            item.style.setProperty('display', 'flex', 'important');
                        } else {
                            item.style.setProperty('display', 'none', 'important');
                        }
                    });
                }

                // Event listener menggunakan input & change
                document.querySelectorAll('.search-pendamping').forEach(input => {
                    input.addEventListener('input', function() {
                        filterPendamping(this.dataset.target, this);
                    });
                });

                document.querySelectorAll('.sort-jabatan').forEach(select => {
                    select.addEventListener('change', function() {
                        filterPendamping(this.dataset.target, this);
                    });
                });
            });
        </script>
    </x-layout>
