<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container-fluid pt-2">
        <div class="row">

            <div class="col-lg-9 pe-lg-2">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm border-0" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="mb-4">
                    <h3 class="fw-bold mb-0 text-dark fs-3">List Pegawai</h3>
                </div>

                <form action="{{ route('kepegawaian.list') }}" method="GET"
                    class="d-flex align-items-center mb-3 gap-2">
                    <input type="text" name="search" value="{{ $search }}" class="form-control border-dark"
                        placeholder="Cari NIP atau Nama..." style="flex: 1;">

                    <button type="submit"
                        class="btn btn-white border-dark d-flex align-items-center gap-2 px-3 fw-medium">
                        <i class="bi bi-search"></i> Cari
                    </button>

                    <span class="ms-2 fw-medium" style="font-size: 0.9rem;">SORT BY</span>
                    <select name="sort" class="form-select border-dark w-auto" onchange="this.form.submit()">
                        <option value="default" {{ $sort == 'default' ? 'selected' : '' }}>Default</option>
                        <option value="nama_asc" {{ $sort == 'nama_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                        <option value="nama_desc" {{ $sort == 'nama_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                    </select>
                </form>

                <div class="table-responsive border border-dark rounded-3 overflow-hidden shadow-sm">
                    <table class="table table-hover mb-0 text-center align-middle">
                        <thead class="border-bottom border-dark" style="background-color: #e9ecef;">
                            <tr>
                                <th class="py-3 text-dark fw-bold">NIP</th>
                                <th class="py-3 text-dark fw-bold">Nama Lengkap</th>
                                <th class="py-3 text-dark fw-bold">Jabatan</th>
                                <th class="py-3 text-dark fw-bold">Bidang</th>
                                <th class="py-3 text-dark fw-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftarPegawai as $pegawai)
                                <tr>
                                    <td class="text-dark">{{ $pegawai->nip }}</td>
                                    <td class="text-dark fw-medium">{{ $pegawai->nama }}</td>
                                    <td class="text-dark">{{ $pegawai->jabatan->nama_jabatan ?? '-' }}</td>
                                    <td class="text-dark">{{ $pegawai->bidang->nama_bidang ?? '-' }}</td>
                                    <td class="p-2">
                                        <div class="d-flex flex-column gap-1 align-items-center">
                                            <button type="button" class="btn btn-primary btn-sm w-100"
                                                style="max-width: 100px; font-size: 0.8rem;" data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $pegawai->nip }}">Lihat Detail</button>

                                            <button type="button" class="btn btn-warning text-white btn-sm w-100"
                                                style="max-width: 100px; font-size: 0.8rem;" data-bs-toggle="modal"
                                                data-bs-target="#editModal{{ $pegawai->nip }}">Edit</button>

                                            <form action="{{ route('kepegawaian.reset_password', $pegawai->nip) }}"
                                                method="POST" class="w-100" style="max-width: 100px;"
                                                id="formReset{{ $pegawai->nip }}">
                                                @csrf
                                                <button type="button" class="btn btn-dark btn-sm w-100"
                                                    style="font-size: 0.8rem;" data-bs-toggle="modal"
                                                    data-bs-target="#resetModal{{ $pegawai->nip }}">
                                                    <i class="bi bi-key-fill me-1 text-warning"></i>Reset Sandi
                                                </button>
                                            </form>

                                            <form action="{{ route('kepegawaian.delete', $pegawai->nip) }}"
                                                method="POST" class="w-100" style="max-width: 100px;"
                                                id="formDelete{{ $pegawai->nip }}">
                                                @csrf
                                                @method('DELETE')
                                                @if(auth()->user()->nip === $pegawai->nip)
                                                    <button type="button" class="btn btn-secondary btn-sm w-100"
                                                        style="font-size: 0.8rem;" disabled>Hapus</button>
                                                @else
                                                    <button type="button" class="btn btn-danger btn-sm w-100"
                                                        style="font-size: 0.8rem;" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $pegawai->nip }}">Hapus</button>
                                                @endif
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="detailModal{{ $pegawai->nip }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-dark rounded-3">
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold fs-4">Detail Pegawai</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body pt-2 px-4 pb-4">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label text-dark fw-medium">NIP</label>
                                                        <input type="text" class="form-control border-dark"
                                                            value="{{ $pegawai->nip }}" disabled>
                                                    </div>
                                                    <div class="col-md-6 mt-3 mt-md-0">
                                                        <label class="form-label text-dark fw-medium">Tanggal
                                                            Lahir</label>
                                                        <input type="date" class="form-control border-dark"
                                                            value="{{ $pegawai->tanggal_lahir }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label text-dark fw-medium">Nama Lengkap</label>
                                                    <input type="text" class="form-control border-dark"
                                                        value="{{ $pegawai->nama }}" disabled>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label text-dark fw-medium">NIP
                                                            Atasan</label>
                                                        <input type="text" class="form-control border-dark"
                                                            value="{{ $pegawai->nip_atasan ?? '-' }}" disabled>
                                                    </div>

                                                    <div class="col-md-6 mt-3 mt-md-0">
                                                        <label class="form-label text-dark fw-medium">Nama
                                                            Atasan</label>
                                                        <input type="text" class="form-control border-dark"
                                                            value="{{ $pegawai->atasan->nama ?? '-' }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label text-dark fw-medium">Email</label>
                                                        <input type="text" class="form-control border-dark"
                                                            value="{{ $pegawai->email ?? '-' }}" disabled>
                                                    </div>
                                                    <div class="col-md-6 mt-3 mt-md-0">
                                                        <label class="form-label text-dark fw-medium">No.
                                                            Telepon</label>
                                                        <input type="text" class="form-control border-dark"
                                                            value="{{ $pegawai->no_telp ?? '-' }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="row mb-4">
                                                    <div class="col-md-6">
                                                        <label class="form-label text-dark fw-medium">Bidang</label>
                                                        <input type="text" class="form-control border-dark"
                                                            value="{{ $pegawai->bidang->nama_bidang ?? '-' }}"
                                                            disabled>
                                                    </div>
                                                    <div class="col-md-6 mt-3 mt-md-0">
                                                        <label class="form-label text-dark fw-medium">Jabatan</label>
                                                        <input type="text" class="form-control border-dark"
                                                            value="{{ $pegawai->jabatan->nama_jabatan ?? '-' }}"
                                                            disabled>
                                                    </div>
                                                </div>
                                                @if ($pegawai->foto_profil)
                                                    <div class="border border-dark rounded-3 p-3 text-center bg-light">
                                                        <i class="bi bi-file-earmark-pdf fs-2 text-dark"></i><br>
                                                        <a href="{{ asset('storage/' . $pegawai->foto_profil) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-secondary mt-2 rounded-pill px-4">Buka
                                                            File</a>
                                                    </div>
                                                @else
                                                    <div
                                                        class="border border-dark rounded-3 p-3 text-center bg-light text-muted">
                                                        <i class="bi bi-file-earmark-x fs-2"></i><br><small>Tidak ada
                                                            berkas
                                                            diunggah</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="editModal{{ $pegawai->nip }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-dark rounded-3">
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold fs-4">Edit Pegawai</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>

                                            <form action="{{ route('kepegawaian.update', $pegawai->nip) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body pt-2 px-4 pb-0">

                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label text-dark fw-medium">NIP</label>
                                                            <input type="text" name="nip"
                                                                class="form-control border-dark angka-only"
                                                                value="{{ $pegawai->nip }}" required>
                                                        </div>
                                                        <div class="col-md-6 mt-3 mt-md-0">
                                                            <label class="form-label text-dark fw-medium">Tanggal
                                                                Lahir</label>
                                                            <input type="date" name="tanggal_lahir"
                                                                class="form-control border-dark"
                                                                value="{{ $pegawai->tanggal_lahir }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label text-dark fw-medium">Nama
                                                            Lengkap</label>
                                                        <input type="text" name="nama"
                                                            class="form-control border-dark"
                                                            value="{{ $pegawai->nama }}" required>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-12">
                                                            <label class="form-label text-dark fw-medium">
                                                                Atasan</label>
                                                            <select name="atasan" class="form-select border-dark">
                                                                <option value="">Tidak Ada Atasan</option>

                                                                @foreach ($daftarAtasan as $atasan)
                                                                    <option value="{{ $atasan->nip }}"
                                                                        {{ $pegawai->nip_atasan == $atasan->nip ? 'selected' : '' }}>
                                                                        {{ $atasan->nip }} - {{ $atasan->nama }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>


                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label text-dark fw-medium">Email <span
                                                                    class="text-secondary small">(Opsional)</span></label>
                                                            <input type="email" name="email"
                                                                class="form-control border-dark"
                                                                value="{{ $pegawai->email }}">
                                                        </div>
                                                        <div class="col-md-6 mt-3 mt-md-0">
                                                            <label class="form-label text-dark fw-medium">No. Telepon
                                                                <span
                                                                    class="text-secondary small">(Opsional)</span></label>
                                                            <input type="text" name="no_telp"
                                                                class="form-control border-dark angka-only"
                                                                value="{{ $pegawai->no_telp }}">
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label
                                                                class="form-label text-dark fw-medium">Bidang</label>
                                                            <select id="bidang" name="bidang"
                                                                class="form-select border-dark border shadow-sm"
                                                                required>
                                                                <option value="" disabled>Pilih Bidang
                                                                </option>
                                                                @foreach ($semuaBidang as $bidang)
                                                                    <option value="{{ $bidang->id_bidang }}"
                                                                        {{ $pegawai->id_bidang == $bidang->id_bidang ? 'selected' : '' }}>
                                                                        {{ $bidang->nama_bidang }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mt-3 mt-md-0">
                                                            <label
                                                                class="form-label text-dark fw-medium">Jabatan</label>
                                                            <select id="jabatan" name="jabatan"
                                                                class="form-select border-dark border shadow-sm"
                                                                required>
                                                                required>
                                                                <option value="" disabled>Pilih Jabatan...
                                                                </option>
                                                                @foreach ($semuaJabatan as $jabatan)
                                                                    <option value="{{ $jabatan->id_jabatan }}"
                                                                        {{ $pegawai->id_jabatan == $jabatan->id_jabatan ? 'selected' : '' }}>
                                                                        {{ $jabatan->nama_jabatan }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label text-dark fw-medium">Timpa Berkas Baru
                                                            (Opsional)
                                                        </label>
                                                        <div
                                                            class="border border-dark rounded-3 p-3 text-center bg-light">
                                                            <input type="file" name="berkas_pegawai"
                                                                class="form-control border-secondary"
                                                                accept=".pdf,.jpg,.jpeg">
                                                            <small class="text-muted d-block mt-1">Biarkan kosong jika
                                                                tidak
                                                                ingin mengubah berkas.</small>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div
                                                    class="modal-footer border-top-0 px-4 pb-4 justify-content-end gap-2">
                                                    <button type="button" class="btn btn-danger fw-bold px-4"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit"
                                                        class="btn btn-success fw-bold px-4">Konfirmasi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="resetModal{{ $pegawai->nip }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4">
                                            <div class="modal-header border-bottom-0 pb-0 mt-2 px-4">
                                                <h5 class="modal-title fw-bold fs-4">Konfirmasi Reset Sandi</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <p class="fs-6">Yakin ingin mereset password
                                                    <strong>{{ $pegawai->nama }}</strong>?
                                                </p>
                                                <p class="text-muted small">Password akan direset menjadi default yaitu
                                                    <strong>Pegawai123</strong>.
                                                </p>
                                            </div>
                                            <div class="modal-footer border-top-0 px-4 pb-4">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" form="formReset{{ $pegawai->nip }}"
                                                    class="btn btn-dark px-4 fw-bold">Reset Sandi</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="deleteModal{{ $pegawai->nip }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4">
                                            <div class="modal-header border-bottom-0 pb-0 mt-2 px-4">
                                                <h5 class="modal-title fw-bold fs-4">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <p class="fs-6">Yakin ingin menghapus pegawai
                                                    <strong>{{ $pegawai->nama }}</strong>?
                                                </p>
                                                <p class="text-danger small"><i
                                                        class="bi bi-exclamation-triangle-fill me-1"></i> Data yang
                                                    dihapus tidak dapat dikembalikan.</p>
                                            </div>
                                            <div class="modal-footer border-top-0 px-4 pb-4">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" form="formDelete{{ $pegawai->nip }}"
                                                    class="btn btn-danger px-4 fw-bold">Hapus</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Tidak ada data pegawai
                                        ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    {{ $daftarPegawai->withQueryString()->links() }}
                </div>
            </div>
            <x-card-agenda :ringkasanAgenda="$ringkasanAgenda" />

        </div>
    </div>
</x-layout>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.angka-only').forEach(function(input) {

            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

        });

        const jabatan = document.getElementById('jabatan');
        const bidang = document.getElementById('bidang');

        function toggleBidang() {

            if (jabatan.value === 'J001') { // Kepala Kantor

                bidang.value = '';
                bidang.disabled = true;
                bidang.removeAttribute('required');

            } else {

                bidang.disabled = false;
                bidang.setAttribute('required', 'required');

            }
        }

        jabatan.addEventListener('change', toggleBidang);

        toggleBidang();
    });
</script>
