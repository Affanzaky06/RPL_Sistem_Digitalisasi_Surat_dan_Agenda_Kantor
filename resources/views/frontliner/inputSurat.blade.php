<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="container-fluid pt-2">
        <div class="row">

            <div class="col-lg-8 pe-lg-4">
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
                <script>
                    setTimeout(() => {
                        document.querySelectorAll('.toast').forEach(el => {
                            el.remove();
                        });
                    }, 6000);
                </script>

                <form action="{{ route('surat.store') }}" method="POST" enctype="multipart/form-data"" method="POST"
                    enctype="multipart/form-data" class="fs-6">
                    @csrf
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold mb-0 text-dark fs-3">
                            Formulir Input Surat Masuk Baru
                        </h3>
                        <select id="jenis_surat" name="jenis_surat"
                            class="form-select w-auto border-dark border shadow-sm" required>
                            <option value="" selected disabled>
                                Pilih Jenis Surat
                            </option>
                            <option value="Undangan">
                                Surat Undangan
                            </option>
                            <option value="Pemberitahuan">
                                Surat Pemberitahuan
                            </option>
                            <option value="Edaran">
                                Surat Edaran
                            </option>
                            <option value="Tugas">
                                Surat Tugas
                            </option>
                        </select>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium">Perihal Surat</label>
                            <input type="text" class="form-control border-dark border" name="perihal" required>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label text-dark fw-medium">Nomor Surat</label>
                            <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}"
                                class="form-control border-dark border @error('nomor_surat') is-invalid @enderror">

                            @error('nomor_surat')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium">Tanggal Surat</label>
                            <input type="date" class="form-control border-dark border" name="tanggal_surat" required>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label text-dark fw-medium">Tanggal Kegiatan</label>
                            <input type="date" class="form-control border-dark border" name="tanggal_kegiatan">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-dark fw-medium">Lokasi</label>
                        <input type="text" class="form-control border-dark border" name="lokasi">
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium">Waktu Mulai</label>
                            <input type="time" class="form-control border-dark border" name="waktu_mulai">
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label text-dark fw-medium">Waktu Selesai</label>
                            <input type="time" class="form-control border-dark border" name="waktu_selesai">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-dark fw-medium">Asal Surat</label>
                        <input type="text" class="form-control border-dark border" name="asal_surat" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Unggah Berkas Scan Surat (PDF/JPG)</label>
                        <div class="border border-dark rounded-3 p-2 text-center" style="background-color: #fafafa;">
                            <i class="bi bi-file-earmark-arrow-up fs-2 text-dark"></i><br>
                            <label for="file-upload" class="btn btn-light border-secondary-subtle mt-2 px-4 shadow-sm"
                                style="cursor: pointer; background-color: #e9ecef;">
                                Upload File
                            </label>
                            <input id="file-upload" type="file" class="d-none" name="berkas_surat"
                                accept=".pdf,.jpg,.jpeg" required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-5 fw-bold"
                            style="background-color: #0d6efd;">Kirim</button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4 ps-lg-4 mt-5 mt-lg-0">
                <h5 class="fw-bold mb-4 text-dark">Ringkasan Agenda dan Peserta</h5>

                <div class="card border-0 mb-3" style="background-color: #f4f5f7; border-radius: 12px;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-0 text-dark">Meeting:</h6>
                        <h6 class="fw-bold mb-2 text-dark">AG-20231015-001</h6>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">
                            Peserta: Budi W. (Keuangan), Susi A. (HR), Andi R. (Operasional)
                        </p>
                    </div>
                </div>

                <div class="card border-0 mb-3" style="background-color: #f4f5f7; border-radius: 12px;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-0 text-dark">Meeting 2:</h6>
                        <h6 class="fw-bold mb-2 text-dark">AG-20231015-001</h6>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">
                            Peserta: Budi W. (Keuangan), Susi A. (HR), Andi R. (Operasional)
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-layout>
