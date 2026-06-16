<x-layout :role="$role" active="profil">
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="container-fluid pt-2">
        <div class="row">
            {{-- KOLOM PROFIL UTAMA --}}
            <div class="col-lg-8 d-flex flex-column align-items-center text-center pe-lg-3 mx-auto" style="max-width: 600px;">
                
                @php
                    $profileAvatar = $user->foto_profil 
                        ? asset('storage/profil/' . $user->foto_profil) 
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user->nama) . '&background=0D8ABC&color=fff&size=200';
                @endphp
                
                <img id="avatar-preview" src="{{ $profileAvatar }}" alt="Foto Profil"
                    class="rounded-circle border border-dark border-1 mb-3 object-fit-cover shadow-sm"
                    style="width: 200px; height: 200px;">

                <form id="form-foto" action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="foto-upload" class="btn btn-secondary btn-sm mb-4"
                        style="cursor: pointer; background-color: #6c757d; border: none;">
                        Ubah Foto
                    </label>
                    <input id="foto-upload" type="file" class="d-none" name="foto_profil" accept=".png,.jpg,.jpeg" onchange="document.getElementById('form-foto').submit();">
                </form>

                <h5 class="fw-bold text-dark mb-1">{{ $user->nama }}</h5>
                <p class="mb-2 text-dark fs-5">{{ $user->nip }}</p>
                <p class="mb-4 text-dark">{{ $role }}</p>

                <hr class="border-dark opacity-75 mb-4" style="width: 60%; border-width: 1px;">

                <div class="w-100" style="max-width: 350px;">

                    <div class="row align-items-center mb-3">
                        <div class="col-2 text-end">
                            <i class="bi bi-telephone fs-5"></i>
                        </div>
                        
                        <div class="col-8 text-start fs-6" id="read-telp">
                            {{ $user->no_telp ?? 'Belum ada no telp' }}
                        </div>
                        <div class="col-2 text-start" id="btn-edit-telp">
                            <a href="javascript:void(0)" onclick="toggleForm('telp')" class="text-dark"><i class="bi bi-pencil-square fs-5"></i></a>
                        </div>

                        <div class="col-10 text-start d-none" id="form-telp">
                            <form action="{{ route('profil.update') }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <input type="text" name="no_telp" class="form-control form-control-sm border-dark-subtle" value="{{ $user->no_telp }}" required placeholder="Masukkan nomor...">
                                <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-lg"></i></button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleForm('telp')"><i class="bi bi-x-lg"></i></button>
                            </form>
                        </div>
                    </div>

                    <div class="row align-items-center mb-3">
                        <div class="col-2 text-end">
                            <i class="bi bi-envelope fs-5"></i>
                        </div>
                        
                        <div class="col-8 text-start fs-6" id="read-email">
                            @if($user->email)
                                <a href="mailto:{{ $user->email }}" class="text-dark text-decoration-underline">{{ $user->email }}</a>
                            @else
                                <span class="text-muted">Belum ada email</span>
                            @endif
                        </div>
                        <div class="col-2 text-start" id="btn-edit-email">
                            <a href="javascript:void(0)" onclick="toggleForm('email')" class="text-dark"><i class="bi bi-pencil-square fs-5"></i></a>
                        </div>

                        <div class="col-10 text-start d-none" id="form-email">
                            <form action="{{ route('profil.update') }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <input type="email" name="email" class="form-control form-control-sm border-dark-subtle" value="{{ $user->email }}" required placeholder="nama@email.com">
                                <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-lg"></i></button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleForm('email')"><i class="bi bi-x-lg"></i></button>
                            </form>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-2 text-end"></div>
                        
                        <div class="col-8 text-start fs-6 text-dark" id="read-password">
                            Ganti Password
                        </div>
                        <div class="col-2 text-start" id="btn-edit-password">
                            <a href="javascript:void(0)" onclick="toggleForm('password')" class="text-dark"><i class="bi bi-pencil-square fs-5"></i></a>
                        </div>

                        <div class="col-10 text-start d-none" id="form-password">
                            <form action="{{ route('profil.update') }}" method="POST" class="d-flex flex-column bg-light p-3 rounded-3 border">
                                @csrf  
                                <div class="input-group input-group-sm mb-2">
                                    <input type="password" id="input-password" name="password" class="form-control border-dark-subtle" placeholder="Password Baru (Min 8)" required>
                                    <button class="btn btn-outline-secondary border-dark-subtle bg-white" type="button" onclick="togglePasswordVisibility('input-password', 'icon-pw1')">
                                        <i class="bi bi-eye-slash text-dark" id="icon-pw1"></i>
                                    </button>
                                </div>

                                <div class="input-group input-group-sm mb-2">
                                    <input type="password" id="input-password-confirm" name="password_confirmation" class="form-control border-dark-subtle" placeholder="Ulangi Password Baru" required>
                                    <button class="btn btn-outline-secondary border-dark-subtle bg-white" type="button" onclick="togglePasswordVisibility('input-password-confirm', 'icon-pw2')">
                                        <i class="bi bi-eye-slash text-dark" id="icon-pw2"></i>
                                    </button>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-1">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleForm('password')">Batal</button>
                                    <button type="submit" class="btn btn-success btn-sm px-3">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

            <x-card-agenda :ringkasanAgenda="$ringkasanAgenda" />

        </div>
    </div>

    <script>
       function toggleForm(field) {
            document.getElementById('read-' + field).classList.toggle('d-none');
            document.getElementById('btn-edit-' + field).classList.toggle('d-none');
            document.getElementById('form-' + field).classList.toggle('d-none');
        }

        // Fungsi memunculkan huruf password (ikon mata)
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === "password") {
                input.type = "text"; // Ubah jadi teks/huruf
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye"); // Ganti ikon mata terbuka
            } else {
                input.type = "password"; // Kembalikan jadi titik-titik
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash"); // Ganti ikon mata dicoret
            }
        }

        
    </script>
</x-layout>