<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="container-fluid pt-2">
        <div class="row">
            
            <div class="col-lg-8 pe-lg-4">
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h3 class="fw-bold mb-0 text-dark fs-3">Formulir Input Surat Masuk Baru</h3>
                    <select class="form-select w-auto border-dark border-1 shadow-sm">
                        <option selected>Surat Undangan</option>
                        <option value="Pemberitahuan">Surat Pemberitahuan</option>
                        <option value="Edaran">Surat Edaran</option>
                        <option value="Tugas">Surat Tugas</option>
                    </select>
                </div>

                <form action="#" method="POST" enctype="multipart/form-data" class="fs-6">
                    @csrf <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium">Perihal Surat</label>
                            <input type="text" class="form-control border-dark border-1" name="perihal">
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label text-dark fw-medium">Nomor Surat</label>
                            <input type="text" class="form-control border-dark border-1" name="nomor_surat">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium">Tanggal Surat</label>
                            <input type="date" class="form-control border-dark border-1" name="tanggal_surat">
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label text-dark fw-medium">Tanggal Kegiatan</label>
                            <input type="date" class="form-control border-dark border-1" name="tanggal_kegiatan">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-dark fw-medium">Lokasi</label>
                        <input type="text" class="form-control border-dark border-1" name="lokasi">
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-medium">Waktu Mulai</label>
                            <input type="time" class="form-control border-dark border-1" name="waktu_mulai">
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label text-dark fw-medium">Waktu Selesai</label>
                            <input type="time" class="form-control border-dark border-1" name="waktu_selesai">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-dark fw-medium">Asal Surat</label>
                        <input type="text" class="form-control border-dark border-1" name="asal_surat">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark fw-medium">Unggah Berkas Scan Surat (PDF/JPG)</label>
                        <div class="border border-dark border-1 rounded-3 p-2 text-center" style="background-color: #fafafa;">
                            <i class="bi bi-file-earmark-arrow-up fs-2 text-dark"></i><br>
                            <label for="file-upload" class="btn btn-light border-secondary-subtle mt-2 px-4 shadow-sm" style="cursor: pointer; background-color: #e9ecef;">
                                Upload File
                            </label>
                            <input id="file-upload" type="file" class="d-none" name="berkas_surat" accept=".pdf,.jpg,.jpeg">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-5 fw-bold" style="background-color: #0d6efd;">Kirim</button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4 ps-lg-4 mt-5 mt-lg-0">
                <h5 class="fw-bold mb-4 text-dark">Ringkasan Agenda dan Peserta</h5>

                <div class="card border-0 mb-3" style="background-color: #f4f5f7; border-radius: 12px;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-0 text-dark">Meeting 1:</h6>
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