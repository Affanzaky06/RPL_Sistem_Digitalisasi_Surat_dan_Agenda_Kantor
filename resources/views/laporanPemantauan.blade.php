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

                <div class="table-responsive">
                    <!-- Tabel disesuaikan dengan desain mockup (tanpa border luar, garis hitam tegas di header) -->
                    <table class="table table-borderless align-middle text-center mb-0" style="border-top: 2px solid #333; border-bottom: 2px solid #333;">
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
                                            @case('J001') Kepala @break
                                            @case('J002') Kabid @break
                                            @case('J003') Subkoor @break
                                            @case('J004') Staff @break
                                            @case('J006') Sekretaris @break
                                            @default -
                                        @endswitch
                                    </td>
                                    
                                    <td class="py-3 text-start">
                                        {{ \Illuminate\Support\Str::limit($item->catatan, 15) }}
                                    </td>
                                    
                                    <td class="py-3">
                                        {{-- LOGIKA WARNA STATUS SESUAI MOCKUP --}}
                                        @if(in_array($item->status, ['Hadir', 'Sudah Diproses', 'ACC', 'Didisposisikan']))
                                            <span class="badge bg-success-subtle text-success border border-success px-3 py-2" style="width: 100px;">ACC</span>
                                        @elseif(in_array($item->status, ['Tidak Hadir', 'Ditolak', 'Dibatalkan']))
                                            <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2" style="width: 100px;">Ditolak</span>
                                        @elseif(in_array($item->status, ['Digantikan', 'Dimaklumi']))
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary px-3 py-2" style="width: 100px;">Selesai</span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2" style="width: 120px;">Dalam Proses</span>
                                        @endif
                                    </td>
                                    
                                    <td class="py-3">
                                        {{-- LOGIKA TOMBOL AKSI --}}
                                        @if(in_array($item->status, ['Tidak Hadir', 'Ditolak']))
                                            @php
                                                $isAttending = \App\Models\Peserta::whereHas('agenda', function($q) use ($item) {
                                                    $q->where('id_surat', $item->id_surat);
                                                })->where('nip', auth()->user()->nip)->exists();
                                            @endphp
                                            <div class="d-flex flex-column gap-1 align-items-center">
                                                <a href="javascript:void(0)" class="text-primary text-decoration-none fw-medium text-center" data-bs-toggle="modal" data-bs-target="#dispoUlangModal{{ $item->id_disposisi }}">
                                                    @if($isAttending) Ganti Pendamping @else Dispo Ulang @endif
                                                </a>
                                                
                                                @if($isAttending)
                                                    {{-- Jika user sudah hadir (ini adalah penolakan pendamping) --}}
                                                    <form action="{{ route('laporan.setujui', $item->id_disposisi) }}" method="POST" id="formSetujui{{ $item->id_disposisi }}">
                                                        @csrf
                                                        <button type="button" class="btn btn-link text-success text-decoration-none p-0 fw-medium" style="font-size: 0.85rem;" data-bs-toggle="modal" data-bs-target="#setujuModal{{ $item->id_disposisi }}">Setujui</button>
                                                    </form>
                                                @else
                                                    {{-- Jika user belum hadir (ini adalah penolakan disposisi biasa) --}}
                                                    <a href="javascript:void(0)" class="text-success text-decoration-none fw-medium" data-bs-toggle="modal" data-bs-target="#hadirModal{{ $item->id_disposisi }}">Hadir</a>
                                                    
                                                    @if(auth()->user()->id_jabatan === 'J002' || auth()->user()->id_jabatan === 'J003')
                                                        <a href="javascript:void(0)" class="text-danger text-decoration-none fw-medium" data-bs-toggle="modal" data-bs-target="#tolakModal{{ $item->id_disposisi }}">Tolak</a>
                                                    @endif
                                                @endif
                                            </div>
                                        @else
                                            <a href="javascript:void(0)" class="text-primary text-decoration-none fw-medium" data-bs-toggle="modal" data-bs-target="#pantauModal{{ $item->id_disposisi }}">Lihat</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-muted">Belum ada riwayat disposisi atau ajakan pendamping.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h4 class="mb-1 fw-semibold">{{ $item->surat->perihal }}</h4>
                                <small class="text-muted">{{ $item->surat->nomor_surat }}</small>
                            </div>
                            <span class="badge px-3 py-2 bg-secondary">{{ $item->status }}</span>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <div class="text-uppercase text-secondary small fw-semibold mb-3">Informasi Surat</div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="text-secondary small">Pengirim</div>
                                    <div>{{ $item->surat->asal_surat }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small">Tanggal Surat</div>
                                    <div>{{ \Carbon\Carbon::parse($item->surat->tanggal_surat)->format('d M Y') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small">Prioritas</div>
                                    <div><span class="badge bg-danger">{{ $item->surat->prioritas }}</span></div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <div class="text-uppercase text-secondary small fw-semibold mb-3">Disposisikan Kepada</div>
                            <div class="border rounded-3 p-3 bg-body-tertiary">
                                <div class="fw-semibold">{{ $item->penerima->nama ?? '-' }}</div>
                                <small class="text-muted">{{ $item->penerima->bidang->nama_bidang ?? '-' }}</small>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <div class="text-uppercase text-secondary small fw-semibold mb-3">Catatan</div>
                            <div class="border rounded-3 p-3 bg-body-tertiary">{{ $item->catatan }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-between w-100">
                            @if (in_array($item->status, ['Menunggu Konfirmasi', 'Belum Dibaca']))
                                @php
                                    // Deteksi route dinamis berdasarkan role pembatal
                                    $rolePrefix = '';
                                    if(Auth::user()->id_jabatan == 'J001') $rolePrefix = 'kepala';
                                    elseif(Auth::user()->id_jabatan == 'J002') $rolePrefix = 'kabid';
                                    elseif(Auth::user()->id_jabatan == 'J003') $rolePrefix = 'subkoor';
                                @endphp
                                <form action="{{ route($rolePrefix.'.disposisi.batal', $item->id_disposisi) }}" method="POST" id="formBatalDisposisi{{ $item->id_disposisi }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#batalDisposisiModal{{ $item->id_disposisi }}">
                                        <i class="bi bi-x-circle me-1"></i> Batalkan Disposisi
                                    </button>
                                </form>
                            @else 
                                <div></div> {{-- Spacer --}}
                            @endif

                            <a href="{{ asset('storage/surat/' . $item->surat->file_scan) }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-eye me-1"></i> Lihat File Surat
                            </a>
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
                            @if(str_contains($item->catatan, 'Pendamping') || \App\Models\Peserta::whereHas('agenda', function($q) use ($item) { $q->where('id_surat', $item->id_surat); })->where('nip', auth()->user()->nip)->exists()) Ganti Pendamping @else Disposisi Ulang Surat @endif
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('laporan.dispo_ulang', $item->id_disposisi) }}" method="POST">
                            @csrf
                            
                            <!-- INFO SURAT -->
                            <div class="border rounded-3 p-3 mb-4 d-flex">
                                <div class="col-6 border-end pe-3">
                                    <small class="text-muted d-block mb-1"><i class="bi bi-send me-1"></i> Pengirim</small>
                                    <div class="fw-bold mb-2">{{ $item->surat->asal_surat }}</div>
                                    <small class="text-muted d-block mb-1"><i class="bi bi-hash me-1"></i> Nomor Surat</small>
                                    <div class="fw-bold mb-2">{{ $item->surat->nomor_surat }}</div>
                                    <small class="text-muted d-block mb-1"><i class="bi bi-file-text me-1"></i> Perihal Surat</small>
                                    <div class="fw-bold">{{ $item->surat->perihal }}</div>
                                </div>
                                <div class="col-6 ps-4 position-relative">
                                    <small class="text-muted d-block mb-1"><i class="bi bi-calendar me-1"></i> Tanggal Surat</small>
                                    <div class="fw-bold mb-2">{{ \Carbon\Carbon::parse($item->surat->tanggal_surat)->format('d-m-Y') }}</div>
                                    <small class="text-muted d-block mb-1"><i class="bi bi-clock me-1"></i> Waktu</small>
                                    <div class="fw-bold mb-2">{{ $item->surat->waktu_mulai_kegiatan ?? '-' }} - {{ $item->surat->waktu_selesai_kegiatan ?? '-' }}</div>
                                    <small class="text-muted d-block mb-1"><i class="bi bi-info-circle me-1"></i> Prioritas</small>
                                    <span class="badge bg-danger">{{ $item->surat->prioritas }}</span>
                                    
                                    <button type="button" class="btn btn-outline-secondary btn-sm position-absolute bottom-0 end-0" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#pantauModal{{ $item->id_disposisi }}">
                                        <i class="bi bi-eye"></i> Lihat Detail
                                    </button>
                                </div>
                            </div>

                            <!-- DAFTAR PEGAWAI BARU -->
                            <div class="border rounded-3 p-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>Pilih yang ingin diDisposisi</h6>
                                    <div class="d-flex gap-2">
                                        <input type="text" class="form-control form-control-sm search-pendamping" data-target="list-pendamping-{{ $item->id_disposisi }}" placeholder="Cari nama..." style="width: 150px;">
                                    </div>
                                </div>
                                
                                <div class="list-group" id="list-pendamping-{{ $item->id_disposisi }}" style="max-height: 180px; overflow-y: auto;">
                                    @foreach ($pegawai as $p)
                                        <label class="list-group-item d-flex gap-3 align-items-center p-3 pendamping-item" data-nama="{{ strtolower($p->nama) }}" style="cursor: pointer;">
                                            {{-- Pakai checkbox agar bisa pilih banyak orang sekaligus jika dibutuhkan --}}
                                            <input class="form-check-input flex-shrink-0 fs-5 mt-0" type="checkbox" name="nip_pendamping[]" value="{{ $p->nip }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <i class="bi bi-person-circle fs-3 text-secondary"></i>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $p->nama }}</h6>
                                                    <small class="text-muted">
                                                        @switch($p->id_jabatan)
                                                            @case('J002') Kabid @break
                                                            @case('J003') Subkoor @break
                                                            @case('J004') Staff @break
                                                            @case('J006') Sekretaris @break
                                                            @default {{ $p->id_jabatan }}
                                                        @endswitch
                                                        @if($p->bidang) | {{ $p->bidang->nama_bidang }} @endif
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
                                <button type="submit" class="btn btn-success px-5 fw-bold" style="background-color: #198754;">Submit</button>
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
                                Dengan memilih <strong>Hadir</strong>, Anda mengambil alih disposisi yang ditolak bawahan ini. Agenda kehadiran akan otomatis dibuat untuk Anda. Anda juga bisa memilih pendamping di bawah ini.
                            </div>
                            
                            <div class="border rounded-3 p-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>Pilih Pendamping (Opsional)</h6>
                                    <input type="text" class="form-control form-control-sm search-pendamping" data-target="list-pendamping-hadir-{{ $item->id_disposisi }}" placeholder="Cari nama..." style="width: 150px;">
                                </div>
                                <div class="list-group" id="list-pendamping-hadir-{{ $item->id_disposisi }}" style="max-height: 180px; overflow-y: auto;">
                                    @foreach ($pegawai as $p)
                                        <label class="list-group-item d-flex gap-3 align-items-center p-3 pendamping-item" data-nama="{{ strtolower($p->nama) }}" style="cursor: pointer;">
                                            <input class="form-check-input flex-shrink-0 fs-5 mt-0" type="checkbox" name="nip_pendamping[]" value="{{ $p->nip }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <i class="bi bi-person-circle fs-3 text-secondary"></i>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $p->nama }}</h6>
                                                    <small class="text-muted">{{ $p->id_jabatan }} @if($p->bidang) | {{ $p->bidang->nama_bidang }} @endif</small>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-success px-5 fw-bold">Konfirmasi Hadir</button>
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
                                Surat ini akan dikembalikan ke atasan Anda (Karena bawahan Anda menolak, dan Anda juga berhalangan).
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Alasan Penolakan</label>
                                <textarea name="alasan_tolak" class="form-control" rows="3" required placeholder="Jelaskan kenapa Anda dan bawahan tidak bisa hadir..."></textarea>
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
                        <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" form="formSetujui{{ $item->id_disposisi }}" class="btn btn-success px-4 fw-bold">Setujui</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="batalDisposisiModal{{ $item->id_disposisi }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4 p-2">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold fs-4 text-danger">Batalkan Disposisi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-3">
                        <p class="fs-6">Yakin ingin membatalkan disposisi ini?</p>
                        <p class="text-danger small"><i class="bi bi-exclamation-triangle-fill me-1"></i> Disposisi akan ditarik kembali dari penerima.</p>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" form="formBatalDisposisi{{ $item->id_disposisi }}" class="btn btn-danger px-4 fw-bold">Ya, Batalkan</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- SCRIPT JAVASCRIPT UNTUK FITUR PENCARIAN NAMA -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.search-pendamping').forEach(input => {
                input.addEventListener('keyup', function() {
                    const term = this.value.toLowerCase();
                    const targetList = document.getElementById(this.dataset.target);
                    const items = targetList.querySelectorAll('.pendamping-item');

                    items.forEach(item => {
                        const nama = item.dataset.nama;
                        item.style.display = nama.includes(term) ? 'flex' : 'none';
                    });
                });
            });
        });
    </script>
</x-layout>