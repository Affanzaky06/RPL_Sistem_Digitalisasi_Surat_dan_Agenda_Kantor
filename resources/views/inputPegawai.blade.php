<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>
    <script src="{{ asset('js/upload-preview.js') }}"></script>
    
    <div class="container-fluid pt-1">
        <div class="row ">

            <div class="col-lg-9 pe-lg-2">
                
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
                @if ($errors->any())
                    <div class="alert alert-danger mb-3 shadow-sm border-0">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <script>
                    setTimeout(() => {
                        document.querySelectorAll('.toast').forEach(el => {
                            el.remove();
                        });
                    }, 6000);
                </script>

                <form action="{{ route('kepegawaian.store') }}" method="POST" enctype="multipart/form-data" class="fs-6">
                    @csrf
                    
                    <div class="mb-2">
                        <h3 class="fw-bold mb-0 text-dark fs-3">
                            Input Data Pegawai Baru
                        </h3>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip') }}" class="form-control border-dark border @error('nip') is-invalid @enderror" placeholder="Contoh: 192501051999001334" required>
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label text-dark fw-medium">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control border-dark border" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control border-dark border" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium">Email <span class="text-secondary small">(Opsional)</span></label>
                            <input type="email" name="email" class="form-control border-dark border" placeholder="Contoh: pegawai@kantor.com">
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label text-dark fw-medium">No. Telepon <span class="text-secondary small">(Opsional)</span></label>
                            <input type="text" name="no_telp" class="form-control border-dark border" placeholder="Contoh: 08123456789">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Alamat</label>
                        <input type="text" name="alamat" class="form-control border-dark border" required>
                    </div>

                   <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium">Bidang</label>
                            <select name="bidang" class="form-select border-dark border shadow-sm" required>
                                <option value="" selected disabled>Pilih Bidang</option>
                                @foreach($semuaBidang as $bidang)
                                    <option value="{{ $bidang->id_bidang }}">{{ $bidang->nama_bidang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label text-dark fw-medium">Jabatan</label>
                            <select name="jabatan" class="form-select border-dark border shadow-sm" required>
                                <option value="" selected disabled>Pilih Jabatan</option>
                                @foreach($semuaJabatan as $jabatan)
                                    <option value="{{ $jabatan->id_jabatan }}">{{ $jabatan->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-dark fw-medium">Unggah Berkas KK, Pas Foto (PDF/JPG)</label>
                        <div class="border border-dark rounded-3 p-4 text-center position-relative" style="background-color: #fafafa;">
                            
                            <div id="upload-prompt">
                                <i class="bi bi-file-earmark-arrow-up fs-2 text-dark"></i><br>
                                <label for="file-upload" class="btn btn-light border-secondary-subtle mt-1 px-3 shadow-sm" style="cursor: pointer; background-color: #e9ecef;">
                                    Upload File
                                </label>
                            </div>

                            <div id="file-preview" class="d-none align-items-center justify-content-center gap-2 mt-2 p-2 border border-secondary-subtle rounded-2 bg-white mx-auto shadow-sm" style="max-width: 350px;">
                                <i class="bi bi-file-earmark-check-fill text-success fs-4"></i>
                                <span id="file-name" class="text-truncate fw-medium text-dark" style="max-width: 200px; font-size: 0.85rem;">nama_file.pdf</span>
                                <button type="button" class="btn btn-sm btn-danger py-0 px-2 ms-2" id="btn-remove-file" title="Batal Upload">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>

                            <input id="file-upload" type="file" class="d-none" name="berkas_pegawai" accept=".pdf,.jpg,.jpeg">
                        </div>
                    </div>

                    <div class="text-end mb-0">
                        <button type="submit" class="btn btn-primary px-5 fw-bold" style="background-color: #0d6efd;">Simpan</button>
                    </div>
                </form>
            </div>

            
            <x-card-agenda :ringkasanAgenda="$ringkasanAgenda"/>
        </div>
    </div>
</x-layout>