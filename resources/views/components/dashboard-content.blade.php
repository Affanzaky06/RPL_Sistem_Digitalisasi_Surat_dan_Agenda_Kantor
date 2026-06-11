{{-- @props([
    'jmlSurat' => 0, 
    'jmlNotif' => 0, 
    'jmlAgenda' => 0
    // Nanti Anda bisa menambahkan 'listAgenda' di sini saat datanya sudah ditarik dari database
]) --}}

<div class="container-fluid pt-2">
        <div class="row">
            
            <div class="col-lg-8">
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm border-secondary-subtle rounded-3">
                            <div class="card-body py-4">
                                <div class="d-flex justify-content-center align-items-center mb-2">
                                    <i class="bi bi-envelope fs-2 me-2"></i>
                                    <h2 class="display-6 mb-0 fw-normal">100</h2>
                                </div>
                                <p class="text-muted mb-0 mt-4" style="font-size: 20px;">Surat Baru</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm border-secondary-subtle rounded-3">
                            <div class="card-body py-4">
                                <div class="d-flex justify-content-center align-items-center mb-2">
                                    <i class="bi bi-bell fs-2 me-2"></i>
                                    <h2 class="display-6 mb-0 fw-normal">100</h2>
                                </div>
                                <p class="text-muted mb-0 mt-4" style="font-size: 20px;">Notifikasi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm border-secondary-subtle rounded-3">
                            <div class="card-body py-4">
                                <div class="d-flex justify-content-center align-items-center mb-2">
                                    <i class="bi bi-calendar-check fs-2 me-2"></i>
                                    <h2 class="display-6 mb-0 fw-normal">100</h2>
                                </div>
                                <p class="text-muted mb-0 mt-4" style="font-size: 20px;">Agenda Mendatang</p>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3 fw-semibold"><i class="bi bi-bell"></i> Notifikasi Terbaru</h5>
                
                <div class="card shadow-sm mb-3 border-secondary-subtle rounded-3">
                    <div class="card-body position-relative">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" style="font-size: 10px;"aria-label="Close"></button>
                        
                        <h6 class="card-title fw-semibold">
                            <i class="bi bi-info-circle"></i> Surat Baru
                        </h6>
                        <p class="card-text text-muted mb-3" style="font-size: 0.9rem;">Rapat Pengadaan Kendaraan</p>
                        <button class="btn btn-dark btn-sm rounded-pill px-4">Detail</button>
                    </div>
                </div>

                <div class="card shadow-sm mb-3 border-secondary-subtle rounded-3">
                    <div class="card-body position-relative">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" style="font-size: 10px;"aria-label="Close"></button>
                        
                        <h6 class="card-title fw-semibold">
                            <i class="bi bi-info-circle"></i> Agenda
                        </h6>
                        <p class="card-text text-muted mb-3" style="font-size: 0.9rem;">Rapat Koordinasi Kamis, 10-10-2026, 10:00, Kantor Bupati</p>
                        <button class="btn btn-dark btn-sm rounded-pill px-4">Detail</button>
                    </div>
                </div>

                <div class="card shadow-sm mb-3 border-secondary-subtle rounded-3">
                    <div class="card-body position-relative">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3"style="font-size: 10px;" aria-label="Close"></button>
                        
                        <h6 class="card-title fw-semibold">
                            <i class="bi bi-info-circle"></i> Laporan Disposisi
                        </h6>
                        <p class="card-text text-muted mb-3" style="font-size: 0.9rem;">Kabid A berhalangan hadir, Musyawarah Desa C</p>
                        <button class="btn btn-dark btn-sm rounded-pill px-4">Detail</button>
                    </div>
                </div>

            </div>


           <x-card-agenda :ringkasanAgenda="$ringkasanAgenda"/>
        </div>
    </div>   