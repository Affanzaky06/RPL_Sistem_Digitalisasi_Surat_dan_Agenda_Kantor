<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>
    <script src="{{ asset('js/upload-preview.js') }}"></script>

    <div class="container-fluid pt-2">
        <div class="row">

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
                <script>
                    setTimeout(() => {
                        document.querySelectorAll('.toast').forEach(el => {
                            el.remove();
                        });
                    }, 6000);
                </script>

                <form action="{{ route('surat.store') }}" method="POST" enctype="multipart/form-data" class="fs-6">
                    @csrf
                    <div class="d-flex justify-content-between align-items-center mb-2">
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
                        </select>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium">Perihal Surat</label>
                            <input type="text" class="form-control border-dark border" name="perihal" required
                                autofocus autocomplete="off">
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label text-dark fw-medium">Nomor Surat</label>
                            <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" autocomplete="off"
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
                            <input type="date" class="form-control border-dark border" name="tanggal_surat" required
                                autocomplete="off">
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label text-dark fw-medium">
                                Tanggal Kegiatan
                                <span id="requiredTanggalKegiatan" class="text-danger d-none">*</span>
                            </label>
                            <input type="date"
                                class="form-control border-dark border @error('tanggal_kegiatan') is-invalid @enderror"
                                name="tanggal_kegiatan" autocomplete="off">
                            @error('tanggal_kegiatan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-dark fw-medium">
                            Lokasi Kegiatan
                            <span id="requiredLokasi" class="text-danger d-none">*</span>
                        </label>
                        <input type="text"
                            class="form-control border-dark border @error('lokasi') is-invalid @enderror" name="lokasi"
                            autocomplete="off">
                        @error('lokasi')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium">
                                Waktu Mulai
                                <span id="requiredWaktuMulai" class="text-danger d-none">*</span>
                            </label>
                            <input type="time"
                                class="form-control border-dark border @error('waktu_mulai') is-invalid @enderror"
                                name="waktu_mulai">
                            @error('waktu_mulai')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label text-dark fw-medium">
                                Waktu Selesai
                                <span id="requiredWaktuSelesai" class="text-danger d-none">*</span>
                            </label>
                            <input type="time"
                                class="form-control border-dark border @error('waktu_selesai') is-invalid @enderror"
                                name="waktu_selesai">
                            @error('waktu_selesai')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-dark fw-medium">Asal Surat</label>
                        <input type="text" class="form-control border-dark border" name="asal_surat" required
                            autocomplete="off">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Unggah Berkas Scan Surat </label>
                        <div class="border rounded-3 p-1 text-center position-relative @error('berkas_surat') border-danger @else border-dark @enderror"
                            style="background-color:#fafafa;">

                            <div id="upload-prompt">
                                <i class="bi bi-file-earmark-arrow-up fs-2 text-dark"></i><br>

                                <label for="file-upload"
                                    class="btn btn-light border-secondary-subtle mt-1 px-3 shadow-sm"
                                    style="cursor: pointer; background-color: #e9ecef;">
                                    Pilih File
                                </label>

                                <div class="small text-muted mt-2">
                                    Format: PDF, JPG, JPEG, PNG
                                    <br>
                                    Maksimal 5 MB
                                </div>
                            </div>

                            <div id="file-preview"
                                class="d-none align-items-center justify-content-center gap-2 mt-2 p-2 border border-secondary-subtle rounded-2 bg-white mx-auto shadow-sm"
                                style="max-width: 350px;">
                                <i class="bi bi-file-earmark-check-fill text-success fs-4"></i>
                                <span id="file-name" class="text-truncate fw-medium text-dark"
                                    style="max-width: 200px; font-size: 0.85rem;">nama_file.pdf</span>
                                <button type="button" class="btn btn-sm btn-danger py-0 px-2 ms-2"
                                    id="btn-remove-file" title="Batal Upload">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>

                            <input id="file-upload" type="file" class="d-none" name="berkas_surat"
                                accept=".pdf,.jpg,.jpeg,.png">
                            @error('berkas_surat')
                                <div class="text-danger mt-2 small">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-5 fw-bold"
                            style="background-color: #0d6efd;">Kirim</button>
                    </div>
                </form>
            </div>

            <x-card-agenda :ringkasanAgenda="$ringkasanAgenda" />

        </div>
    </div>
</x-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const jenisSurat =
            document.getElementById('jenis_surat');

        const tanggalKegiatan =
            document.querySelector('[name="tanggal_kegiatan"]');

        const lokasi =
            document.querySelector('[name="lokasi"]');

        const waktuMulai =
            document.querySelector('[name="waktu_mulai"]');

        const waktuSelesai =
            document.querySelector('[name="waktu_selesai"]');

        function toggleUndanganFields() {

            const isUndangan =
                jenisSurat.value === 'Undangan';

            tanggalKegiatan.required = isUndangan;
            lokasi.required = isUndangan;
            waktuMulai.required = isUndangan;
            waktuSelesai.required = isUndangan;

            document.getElementById(
                'requiredTanggalKegiatan'
            ).classList.toggle(
                'd-none',
                !isUndangan
            );

            document.getElementById(
                'requiredLokasi'
            ).classList.toggle(
                'd-none',
                !isUndangan
            );

            document.getElementById(
                'requiredWaktuMulai'
            ).classList.toggle(
                'd-none',
                !isUndangan
            );

            document.getElementById(
                'requiredWaktuSelesai'
            ).classList.toggle(
                'd-none',
                !isUndangan
            );
        }

        jenisSurat.addEventListener(
            'change',
            toggleUndanganFields
        );

        toggleUndanganFields();

    });
</script>
