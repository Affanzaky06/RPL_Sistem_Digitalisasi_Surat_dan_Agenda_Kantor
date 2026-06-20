<x-layout :role="$role">

    <x-slot:title>
        {{ $title }}
    </x-slot:title>

    <div class="container-fluid">
        <div class="row">
            {{-- KONTEN UTAMA --}}
            <div class="col-lg-9">
                @if (session('success'))
                    <div class="position-fixed top-0 end-0 p-3" style="z-index:9999;">
                        <div class="toast show border-0 shadow">
                            <div class="toast-body">
                                <span class="text-success">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                </span>
                                {{ session('success') }}
                            </div>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="position-fixed top-0 end-0 p-3" style="z-index:9999;">
                        <div class="toast show border-0 shadow">
                            <div class="toast-body">
                                <span class="text-danger">
                                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                                </span>
                                {{ session('error') }}
                            </div>
                        </div>
                    </div>
                @endif
                <script>
                    setTimeout(() => {
                        document.querySelectorAll('.toast').forEach(el => {
                            el.remove();
                        });
                    }, 6000);
                </script>

                <h3 class="fw-bold mb-4">
                    Surat Masuk dan Disposisi
                </h3>
                
                <div class="border border-dark rounded-3 overflow-hidden shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless align-middle text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold fs-6">Nomor Surat</th>
                                    <th class="fw-semibold fs-6">Pengirim</th>
                                    <th class="fw-semibold fs-6">Perihal</th>
                                    <th class="fw-semibold fs-6">Prioritas</th>
                                    <th class="fw-semibold fs-6">Tanggal Kegiatan</th>
                                    <th class="fw-semibold fs-6">Pendisposisi</th>
                                    <th class="fw-semibold fs-6">Detail Surat</th>
                                    <th class="fw-semibold fs-6">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="fs-6 bg-white">
                                @forelse ($suratMasuk as $surat)
                                    @php
                                        $dispo = $surat->disposisi->where('nip_penerima', auth()->user()->nip)->first();
                                    @endphp

                                    <tr style="border-bottom:1px solid #dee2e6;">
                                        <td>{{ $surat->nomor_surat }}</td>
                                        <td>{{ $surat->asal_surat }}</td>
                                        <td>{{ $surat->perihal }}</td>
                                        <td>
                                            @if ($surat->prioritas == 'Tinggi')
                                                <span class="badge bg-danger">Tinggi</span>
                                            @elseif($surat->prioritas == 'Sedang')
                                                <span class="badge bg-warning text-dark">Sedang</span>
                                            @else
                                                <span class="badge bg-success">Rendah</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($surat->tanggal_kegiatan)
                                                {{ \Carbon\Carbon::parse($surat->tanggal_kegiatan)->format('d-m-Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($dispo)
                                                {{ $dispo->pemberi->nama }} <br>
                                                <small class="text-muted">{{ $dispo->pemberi->bidang->nama_bidang ?? 'Kepala Kantor' }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-dark btn-sm" style="width:100px;" data-bs-toggle="modal" data-bs-target="#detailModal{{ $surat->id_surat }}">
                                                Detail
                                            </button>
                                        </td>
                                        <td>
                                            @php
                                                // CEK REAL-TIME: Apakah baris surat ini adalah undangan pendampingan dari Kepala untuk user ini?
                                                $isDiajakPendamping = \App\Models\Peserta::where('nip', auth()->user()->nip)
                                                    ->whereHas('agenda', function($q) use ($surat) {
                                                        $q->where('id_surat', $surat->id_surat);
                                                    })
                                                    ->where('status_kehadiran', 'Menunggu Konfirmasi')
                                                    ->exists();
                                            @endphp

                                            @if ($isDiajakPendamping)
                                                <div class="d-flex flex-column align-items-center gap-1">
                                                    <form action="{{ route('pendamping.konfirmasi', [$surat->id_surat, 'Hadir']) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm" style="width:100px;">
                                                            <i class="bi bi-check-lg"></i> Hadir
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('pendamping.konfirmasi', [$surat->id_surat, 'Tolak']) }}" method="POST" onsubmit="return confirm('Tolak ajakan pendampingan Atasan?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm" style="width:100px;">
                                                            <i class="bi bi-x"></i> Tolak
                                                        </button>
                                                    </form>
                                                    
                                                    <small class="text-primary fw-bold mt-1" style="font-size: 0.7rem;"><i class="bi bi-info-circle"></i> Diajak Pendamping</small>
                                                </div>
                                            @else
                                                <div class="d-flex flex-column align-items-center gap-1">
                                                    @if ($surat->jenis_surat == 'Undangan')
                                                        <button class="btn btn-primary btn-sm" style="width:100px;" data-bs-toggle="modal" data-bs-target="#disposisiModal{{ $surat->id_surat }}">
                                                            Disposisi
                                                        </button>
                                                        <button type="button" class="btn btn-success btn-sm" style="width:100px;" data-bs-toggle="modal" data-bs-target="#hadirModal{{ $surat->id_surat }}">
                                                            Hadir
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" style="width:100px;" data-bs-toggle="modal" data-bs-target="#tolakModal{{ $surat->id_surat }}">
                                                            Tolak
                                                        </button>
                                                    @else
                                                        <button class="btn btn-primary btn-sm" style="width:100px;" data-bs-toggle="modal" data-bs-target="#disposisiModal{{ $surat->id_surat }}">
                                                            Disposisi
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">Belum ada surat masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR KANAN --}}
            <x-card-agenda :ringkasanAgenda="$ringkasanAgenda" />
        </div>
    </div>

    @foreach ($suratMasuk as $surat)
        <div class="modal fade" id="detailModal{{ $surat->id_surat }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header">
                        <h5 class="modal-title"> Detail Surat </h5> <button type="button" class="btn-close" data-bs-dismiss="modal"> </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h4 class="mb-1 fw-semibold"> {{ $surat->perihal }} </h4> <small class="text-muted"> {{ $surat->nomor_surat }} </small>
                            </div>
                            @if ($surat->status == 'Terverifikasi')
                                <span class="badge bg-success px-3 py-2" style="width:110px;font-size:0.85rem;">Terverifikasi</span>
                            @else
                                <span class="badge bg-danger px-3 py-2" style="width:110px;font-size:0.85rem;">Ditolak</span>
                            @endif
                        </div>
                        <hr>
                        <div class="mb-4">
                            <div class="text-uppercase text-secondary small fw-semibold mb-3"> Informasi Surat </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="text-secondary small">Nomor Surat</div>
                                    <div>{{ $surat->nomor_surat }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small">Asal Surat</div>
                                    <div>{{ $surat->asal_surat }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small">Tanggal Surat</div>
                                    <div> {{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d M Y') }} </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small">Jenis Surat</div>
                                    <div>{{ $surat->jenis_surat }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small">Prioritas</div>
                                    <div>
                                        @if ($surat->prioritas == 'Tinggi')
                                            <span class="badge bg-danger px-3 py-2" style="width:110px;font-size:0.85rem;">Tinggi</span>
                                        @elseif($surat->prioritas == 'Sedang')
                                            <span class="badge bg-warning text-dark px-3 py-2" style="width:110px;font-size:0.85rem;">Sedang</span>
                                        @else
                                            <span class="badge bg-success px-3 py-2" style="width:110px;font-size:0.85rem;">Rendah</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small">Tanggal Verifikasi</div>
                                    <div>{{ \Carbon\Carbon::parse($surat->tanggal_verifikasi)->format('d M Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                        @if ($surat->jenis_surat == 'Undangan')
                            <hr>
                            <div class="mb-4">
                                <div class="text-uppercase text-secondary small fw-semibold mb-3"> Informasi Kegiatan</div>
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="text-secondary small">Tanggal Surat</div>
                                        <div>{{ \Carbon\Carbon::parse($surat->tanggal_kegiatan)->format('d M Y') }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-secondary small">Waktu Mulai</div>
                                        <div>{{ $surat->waktu_mulai_kegiatan }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-secondary small">Waktu Selesai</div>
                                        <div>{{ $surat->waktu_selesai_kegiatan }}</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="text-secondary small">Lokasi</div>
                                        <div>{{ $surat->lokasi_kegiatan }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <hr>
                        <div>
                            <div class="text-uppercase text-secondary small fw-semibold mb-3"> Lampiran Surat </div>
                            <div class="border rounded-3 p-3 bg-body-tertiary">
                                <div class="d-flex align-items-center"> 
                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-1 me-3"></i>
                                    <div>
                                        <div class="fw-semibold"> {{ $surat->file_scan }} </div> 
                                        <small class="text-muted"> Berkas Scan Surat </small>
                                    </div>
                                </div> 
                                <a href="{{ asset('storage/surat/' . $surat->file_scan) }}" target="_blank" class="btn btn-primary btn-sm mt-3"> 
                                    <i class="bi bi-eye me-1"></i> Lihat File
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($suratMasuk as $surat)
        <div class="modal fade" id="disposisiModal{{ $surat->id_surat }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <form action="{{ route('kabid.disposisi', $surat->id_surat) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Disposisi Surat</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h4 class="mb-1 fw-semibold">{{ $surat->perihal }}</h4>
                                    <small class="text-muted">{{ $surat->nomor_surat }}</small>
                                </div>
                                @if ($surat->prioritas == 'Tinggi')
                                    <span class="badge bg-danger px-3 py-2" style="width:110px;font-size:0.85rem;">Tinggi</span>
                                @elseif($surat->prioritas == 'Sedang')
                                    <span class="badge bg-warning text-dark px-3 py-2" style="width:110px;font-size:0.85rem;">Sedang</span>
                                @else
                                    <span class="badge bg-success px-3 py-2" style="width:110px;font-size:0.85rem;">Rendah</span>
                                @endif
                            </div>
                            <hr>
                            <div class="mb-4">
                                <div class="text-uppercase text-secondary small fw-semibold mb-3">Informasi Surat</div>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="text-secondary small">Pengirim</div>
                                        <div>{{ $surat->asal_surat }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-secondary small">Tanggal Surat</div>
                                        <div>{{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d M Y') }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-secondary small">Nomor Surat</div>
                                        <div>{{ $surat->nomor_surat }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-secondary small">Jenis Surat</div>
                                        <div>{{ $surat->jenis_surat }}</div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="mb-4">
                                <div class="text-uppercase text-secondary small fw-semibold mb-3">Tujuan Disposisi</div>
                                <select name="nip_penerima" class="form-select" required>
                                    <option value="" selected disabled>Pilih Penerima</option>
                                    @php
                                        $ditolak = $surat->disposisi->where('status', 'Tidak Hadir')->pluck('nip_penerima')->toArray();
                                    @endphp
                                    @foreach ($pegawai as $p)
                                        @if (!in_array($p->nip, $ditolak))
                                            <option value="{{ $p->nip }}">
                                                {{ $p->nama }} -
                                                @switch($p->id_jabatan)
                                                    @case('J003') Subkoor @break
                                                    @case('J004') Staff @break
                                                @endswitch
                                                @if ($p->bidang) | {{ $p->bidang->nama_bidang }} @endif
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <div class="text-uppercase text-secondary small fw-semibold mb-3">Catatan Disposisi</div>
                                <textarea name="catatan" rows="4" class="form-control" placeholder="Tulis catatan disposisi..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Disposisikan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($suratMasuk as $surat)
        <div class="modal fade" id="hadirModal{{ $surat->id_surat }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4 p-2">
                    <form action="{{ route('kabid.konfirmasi_hadir', $surat->id_surat) }}" method="POST">
                        @csrf
                        <div class="modal-header border-0 pb-0">
                            <h5 class="fw-bold fs-4">Ajak Pendamping</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="border rounded-3 p-3 mb-4 d-flex position-relative">
                                <div class="col-6 border-end pe-3">
                                    <small class="text-muted d-block mb-1"><i class="bi bi-send me-2"></i>Pengirim</small>
                                    <div class="fw-bold mb-3 text-dark">{{ $surat->asal_surat }}</div>
                                    
                                    <small class="text-muted d-block mb-1"><i class="bi bi-hash me-2"></i>Nomor Surat</small>
                                    <div class="fw-bold mb-3 text-dark">{{ $surat->nomor_surat }}</div>
                                    
                                    <small class="text-muted d-block mb-1"><i class="bi bi-file-earmark-text me-2"></i>Perihal Surat</small>
                                    <div class="fw-bold text-dark">{{ $surat->perihal }}</div>
                                </div>
                                <div class="col-6 ps-4 position-relative">
                                    <small class="text-muted d-block mb-1"><i class="bi bi-calendar me-2"></i>Tanggal Surat</small>
                                    <div class="fw-bold mb-3 text-dark">{{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d-m-Y') }}</div>
                                    
                                    <small class="text-muted d-block mb-1"><i class="bi bi-clock me-2"></i>Waktu</small>
                                    <div class="fw-bold mb-3 text-dark">
                                        {{ $surat->waktu_mulai_kegiatan ? \Carbon\Carbon::parse($surat->waktu_mulai_kegiatan)->format('H:i') : '-' }} - 
                                        {{ $surat->waktu_selesai_kegiatan ? \Carbon\Carbon::parse($surat->waktu_selesai_kegiatan)->format('H:i') : '-' }}
                                    </div>
                                    
                                    <small class="text-muted d-block mb-1"><i class="bi bi-info-circle me-2"></i>Prioritas</small>
                                    @if ($surat->prioritas == 'Tinggi')
                                        <span class="badge bg-danger px-3 py-1">Urgent</span>
                                    @elseif($surat->prioritas == 'Sedang')
                                        <span class="badge bg-warning text-dark px-3 py-1">Sedang</span>
                                    @else
                                        <span class="badge bg-success px-3 py-1">Rendah</span>
                                    @endif

                                    <button type="button" class="btn btn-outline-secondary btn-sm position-absolute bottom-0 end-0" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#detailModal{{ $surat->id_surat }}">
                                        <i class="bi bi-eye"></i> Lihat Detail
                                    </button>
                                </div>
                            </div>

                            <div class="border rounded-3 p-3 mb-4 bg-white shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="fw-bold mb-0 d-flex align-items-center text-dark">
                                        <i class="bi bi-people me-2 fs-5"></i> Pilih Pendamping
                                    </label>
                                    
                                    <div class="d-flex gap-2">
                                        <div class="input-group input-group-sm border rounded-2" style="width: 200px;">
                                            <input type="text" class="form-control border-0 shadow-none search-pendamping" data-target="list-pendamping-{{ $surat->id_surat }}" placeholder="Cari nama...">
                                            <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                                        </div>
                                        <select class="form-select form-select-sm border filter-jabatan shadow-none" data-target="list-pendamping-{{ $surat->id_surat }}" style="width: 140px;">
                                            <option value="ALL">Pilih Jabatan</option>
                                            <option value="J003">Subkoor</option>
                                            <option value="J004">Staff</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="list-group list-group-flush" id="list-pendamping-{{ $surat->id_surat }}" style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px;">
                                    @foreach ($pegawai as $p)
                                        <label class="list-group-item d-flex gap-3 align-items-center p-3 pendamping-item-row" data-nama="{{ strtolower($p->nama) }}" data-jabatan="{{ $p->id_jabatan }}" style="cursor: pointer; transition: 0.2s;">
                                            <input class="form-check-input flex-shrink-0 fs-5 mt-0 border-secondary check-pendamping" type="checkbox" name="nip_pendamping[]" value="{{ $p->nip }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <i class="bi bi-person-circle fs-2 text-secondary"></i>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $p->nama }}</h6>
                                                    <small class="text-muted">
                                                        @switch($p->id_jabatan)
                                                            @case('J003') Subkoor @break
                                                            @case('J004') Staff @break
                                                            @default {{ $p->id_jabatan }}
                                                        @endswitch
                                                        @if ($p->bidang) | {{ $p->bidang->nama_bidang }} @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="border rounded-3 p-3 bg-white shadow-sm">
                                <label class="fw-bold mb-2 d-flex align-items-center text-dark">
                                    <i class="bi bi-journal-text me-2 fs-5"></i> Catatan
                                </label>
                                <textarea name="catatan" rows="2" class="form-control border-secondary-subtle" placeholder="Tulis Catatan Disini... (kosongkan jika tidak ada)"></textarea>
                            </div>
                        </div>

                        <div class="text-end px-4 pb-4 mt-2">
                            <button type="submit" class="btn btn-success px-5 fw-bold" style="background-color: #198754; border-radius: 8px;">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($suratMasuk as $surat)
        <div class="modal fade" id="tolakModal{{ $surat->id_surat }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <form action="{{ route('kabid.tolak', $surat->id_surat) }}" method="POST">
                        @csrf
                        <div class="modal-header border-bottom-0 pb-0 mt-2 px-4">
                            <h5 class="modal-title fw-bold fs-4">Tolak Surat</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="border rounded-3 p-3 mb-4 bg-white shadow-sm d-flex position-relative">
                                <div class="col-6 pe-3" style="border-right: 2px dashed #dee2e6;">
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-send me-2"></i>Pengirim</small>
                                        <span class="fw-bold text-dark">{{ $surat->asal_surat }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-hash me-2"></i>Nomor Surat</small>
                                        <span class="fw-bold text-dark">{{ $surat->nomor_surat }}</span>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block mb-1"><i class="bi bi-file-earmark-text me-2"></i>Perihal Surat</small>
                                        <span class="fw-bold text-dark">{{ $surat->perihal }}</span>
                                    </div>
                                </div>
                                <div class="col-6 ps-4 position-relative">
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-calendar me-2"></i>Tanggal Surat</small>
                                        <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d-m-Y') }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-clock me-2"></i>Waktu</small>
                                        <span class="fw-bold text-dark">
                                            {{ $surat->waktu_mulai_kegiatan ? \Carbon\Carbon::parse($surat->waktu_mulai_kegiatan)->format('H:i') : '-' }} - 
                                            {{ $surat->waktu_selesai_kegiatan ? \Carbon\Carbon::parse($surat->waktu_selesai_kegiatan)->format('H:i') : '-' }}
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block mb-1"><i class="bi bi-info-circle me-2"></i>Prioritas</small>
                                        @if ($surat->prioritas == 'Tinggi')
                                            <span class="badge bg-danger px-3 py-1">Urgent</span>
                                        @elseif($surat->prioritas == 'Sedang')
                                            <span class="badge bg-warning text-dark px-3 py-1">Sedang</span>
                                        @else
                                            <span class="badge bg-success px-3 py-1">Rendah</span>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm position-absolute bottom-0 end-0 me-3" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#detailModal{{ $surat->id_surat }}">
                                        <i class="bi bi-eye"></i> Lihat Detail
                                    </button>
                                </div>
                            </div>
                            <div class="border rounded-3 p-3 bg-white shadow-sm">
                                <label class="fw-bold mb-2 d-flex align-items-center text-dark">
                                    <i class="bi bi-pencil-square me-2 fs-5"></i> Alasan Menolak Surat
                                </label>
                                <textarea name="alasan_tolak" rows="4" class="form-control border-secondary-subtle" placeholder="Tulis Alasan Disini..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 px-4 pb-4 justify-content-end">
                            <button type="submit" class="btn btn-success px-4 fw-bold" style="background-color: #198754;">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

</x-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Logika Search Bar Disposisi & Pendamping
        document.querySelectorAll('.search-penerima, .search-pendamping').forEach(input => {
            input.addEventListener('keyup', function() {
                const term = this.value.toLowerCase();
                const targetList = document.getElementById(this.dataset.target);
                // Menyesuaikan target item beda class
                const isPendamping = this.classList.contains('search-pendamping');
                const itemClass = isPendamping ? '.pendamping-item-row' : '.penerima-item';
                
                const items = targetList.querySelectorAll(itemClass);
                items.forEach(item => {
                    const nama = item.dataset.nama;
                    item.style.display = nama.includes(term) ? 'flex' : 'none';
                });
            });
        });

        // Logika Dropdown Filter Jabatan Pendamping
        document.querySelectorAll('.filter-jabatan').forEach(select => {
            select.addEventListener('change', function() {
                const jabatan = this.value;
                const targetList = document.getElementById(this.dataset.target);
                const items = targetList.querySelectorAll('.pendamping-item-row');

                items.forEach(item => {
                    if (jabatan === 'ALL' || item.dataset.jabatan === jabatan) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Script Efek Highlight Hijau Saat Checkbox Diklik (Sesuai Mockup)
        document.querySelectorAll('.check-pendamping').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const row = this.closest('.pendamping-item-row');
                if (this.checked) {
                    // Beri warna hijau muda dan border hijau
                    row.classList.add('bg-success-subtle', 'border', 'border-success');
                    row.classList.remove('border-secondary-subtle');
                } else {
                    // Kembalikan ke warna asli
                    row.classList.remove('bg-success-subtle', 'border', 'border-success');
                    row.classList.add('border-secondary-subtle');
                }
            });
        });

    });
</script>