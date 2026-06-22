<x-layout :role="$role">
    <x-slot:title>Kelola Data Bidang</x-slot:title>

<div class="container-fluid pt-2">
    <div class="row">
        <div class="col-lg-12">
            <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                <!-- Header Section -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-diagram-3 text-primary fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">Data Bidang</h4>
                        <p class="mb-0 text-secondary small">Manajemen master data bidang struktur organisasi</p>
                    </div>
                </div>
                
                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#tambahModal">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Bidang</span>
                </button>
            </div>

            <!-- Pesan Sukses / Error -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Search Bar -->
            <div class="row mb-4">
                <div class="col-md-5">
                    <form action="{{ route('kepegawaian.bidang') }}" method="GET" class="d-flex gap-2">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama bidang..." value="{{ $search ?? '' }}">
                        </div>
                        <button type="submit" class="btn btn-dark px-4">Cari</button>
                        @if($search)
                            <a href="{{ route('kepegawaian.bidang') }}" class="btn btn-outline-secondary px-3" title="Reset">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Table Section -->
            <div class="table-responsive rounded-3 border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4 text-center" style="width: 5%">No</th>
                            <th class="py-3 px-4" style="width: 15%">ID Bidang</th>
                            <th class="py-3 px-4">Nama Bidang</th>
                            <th class="py-3 px-4 text-center" style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($daftarBidang as $index => $bidang)
                        <tr>
                            <td class="px-4 text-center text-muted">{{ $daftarBidang->firstItem() + $index }}</td>
                            <td class="px-4">
                                <span class="badge bg-secondary text-white fw-normal px-2 py-1 rounded-pill">{{ $bidang->id_bidang }}</span>
                            </td>
                            <td class="px-4 fw-medium">{{ $bidang->nama_bidang }}</td>
                            <td class="px-4 text-center">
                                <div class="btn-group shadow-sm">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $bidang->id_bidang }}" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $bidang->id_bidang }}" title="Hapus">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editModal{{ $bidang->id_bidang }}" tabindex="-1" aria-labelledby="editModalLabel{{ $bidang->id_bidang }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-light border-bottom-0 pb-3">
                                        <h5 class="modal-title fw-bold" id="editModalLabel{{ $bidang->id_bidang }}">
                                            <i class="bi bi-pencil-square text-primary me-2"></i>Edit Data Bidang
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('kepegawaian.bidang.update', $bidang->id_bidang) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body px-4 py-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-medium text-secondary small">ID Bidang (Auto)</label>
                                                <input type="text" class="form-control bg-light" value="{{ $bidang->id_bidang }}" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-medium">Nama Bidang <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_bidang" class="form-control" value="{{ $bidang->nama_bidang }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-top-0 pt-3 pb-3 px-4">
                                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Delete -->
                        <div class="modal fade" id="deleteModal{{ $bidang->id_bidang }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-danger text-white border-bottom-0 pb-3">
                                        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-4 py-4 text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-trash3 text-danger" style="font-size: 3rem;"></i>
                                        </div>
                                        <h5 class="fw-bold mb-2">Hapus Bidang "{{ $bidang->nama_bidang }}"?</h5>
                                        <p class="text-muted mb-0">Tindakan ini tidak dapat dibatalkan. Pastikan tidak ada pegawai yang terdaftar pada bidang ini.</p>
                                    </div>
                                    <div class="modal-footer bg-light border-top-0 pt-3 pb-3 px-4 d-flex justify-content-center gap-2">
                                        <form action="{{ route('kepegawaian.bidang.delete', $bidang->id_bidang) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger px-4">Ya, Hapus Data</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-diagram-3 text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                    <h5 class="text-muted fw-medium mb-1">Data bidang tidak ditemukan</h5>
                                    @if($search)
                                        <p class="text-muted small">Coba gunakan kata kunci pencarian yang lain.</p>
                                    @else
                                        <p class="text-muted small">Belum ada data bidang yang ditambahkan ke sistem.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-4">
                {{ $daftarBidang->appends(['search' => $search])->links('pagination::bootstrap-5') }}
            </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Bidang -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0 pb-3">
                <h5 class="modal-title fw-bold" id="tambahModalLabel">
                    <i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah Data Bidang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kepegawaian.bidang.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-4">
                    <div class="alert alert-info border-0 d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                        <div>ID Bidang akan di-generate otomatis oleh sistem secara berurutan.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Bidang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_bidang" class="form-control" placeholder="Masukkan nama bidang..." required autofocus>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 pt-3 pb-3 px-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Bidang</button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-layout>
