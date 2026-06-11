<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="container-fluid pt-2">
        <div class="row">

            <div class="col-lg-8 d-flex flex-column align-items-center text-center pe-lg-3 mx-5">

                <img src="https://ilmutanah.upnyk.ac.id/public/assets/dosen/thumb/9204738615.png" alt="Foto Profil"
                    class="rounded-circle border border-dark border-1 mb-3 object-fit-cover shadow-sm"
                    style="width: 200px; height: 200px;">

                <label for="foto-upload" class="btn btn-secondary btn-sm mb-4"
                    style="cursor: pointer; background-color: #6c757d; border: none;">
                    Ubah Foto
                </label>

                <input id="foto-upload" type="file" class="d-none" name="foto_profil" accept=".png,.jpg,.jpeg">

                <h5 class="fw-bold text-dark mb-1">{{ auth()->user()->nama ?? 'JKW YNTKTS UHH KAGET' }}</h5>
                <p class="mb-2 text-dark fs-5">{{ auth()->user()->nip ?? '192501051999001334' }}</p>
                <p class="mb-4 text-dark">{{ $role ?? 'Frontliner' }}</p>

                <hr class="border-dark opacity-75 mb-4" style="width: 60%; border-width: 1px;">

                <div class="w-100" style="max-width: 350px;">

                    <div class="row align-items-center mb-3">
                        <div class="col-2 text-end">
                            <i class="bi bi-telephone fs-5"></i>
                        </div>
                        <div class="col-8 text-start fs-6">
                            0856445516160651
                        </div>
                        <div class="col-2 text-start">
                            <a href="#" class="text-dark"><i class="bi bi-pencil-square fs-5"></i></a>
                        </div>
                    </div>

                    <div class="row align-items-center mb-3">
                        <div class="col-2 text-end">
                            <i class="bi bi-envelope fs-5"></i>
                        </div>
                        <div class="col-8 text-start fs-6">
                            <a href="#" class="text-dark text-decoration-underline">YNTKTS@gmail.com</a>
                        </div>
                        <div class="col-2 text-start">
                            <a href="#" class="text-dark"><i class="bi bi-pencil-square fs-5"></i></a>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-2 text-end">
                        </div>
                        <div class="col-8 text-start fs-6 text-dark">
                            Ganti Password
                        </div>
                        <div class="col-2 text-start">
                            <a href="#" class="text-dark"><i class="bi bi-pencil-square fs-5"></i></a>
                        </div>
                    </div>

                </div>
            </div>

            <x-card-agenda :ringkasanAgenda="$ringkasanAgenda" />

        </div>
    </div>
</x-layout>
