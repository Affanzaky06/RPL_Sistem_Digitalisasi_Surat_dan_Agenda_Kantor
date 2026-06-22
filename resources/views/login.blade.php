<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('Lambang_Kabupaten_Blora copy.png') }}">
    <link rel="stylesheet" href="{{ asset('css/custom-login.css') }}">
    <title>Login</title>
</head>

<body>
    <div class="bg-overlay d-flex justify-content-center align-items-center min-vh-100">
        <div class="card login-card shadow-lg">
            <div class="card-body">
                <div class="text-center mb-3">
                    <img src="{{ asset('Lambang_Kabupaten_Blora copy.png') }}" alt="Logo" style="width: 130px;">
                </div>

                <h5 class=" text-center text-white mb-2" style="font-weight: 500;">Sistem Surat Dan Agenda Digital</h5>

                    <form action="{{ route('login.post') }}" method="POST" class="row g-3 needs-validation" novalidate>
                        @csrf

                        @if($errors->has('NIP'))
                            <div class="alert alert-danger py-2 text-center mb-0 position-relative mt-2 fw-medium shadow-sm"
                                style="font-size: 0.85rem; border-radius: 8px;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first('NIP') }}
                            </div>
                        @endif

                        <div class="mb-3 position-relative mt-4">
                            <input type="text" id="validationCustom01" class="form-control custom-input py-2" name="NIP"
                                placeholder="NIP" value="{{ old('NIP') }}" required autocomplete="off">
                            <div class="invalid-feedback text-start" style="font-size: 0.85rem;">
                                NIP tidak boleh kosong.
                            </div>
                        </div>

                        <div class="mb-4 position-relative">
                            <input type="password" class="form-control custom-input py-2" name="password"
                                placeholder="Password" required>
                            <div class="invalid-feedback text-start" style="font-size: 0.85rem;">
                                Password tidak boleh kosong.
                            </div>
                        </div>

                        <div class="text-center mt-2">
                            <button type="submit" class="btn btn-success text-white">Login</button>
                        </div>
                        
                        <div class="text-center mt-3">
                            <small class="text-white-50" style="font-size: 0.8rem;">
                                Lupa password? Hubungi bagian <b>Kepegawaian</b> untuk mereset sandi Anda.
                            </small>
                        </div>
                    </form>

                </div>
            </div>
        </div>
</body>
<script>
    (function () {
        'use strict'

        var forms = document.querySelectorAll('.needs-validation')


        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    // Tambahkan class was-validated untuk memunculkan warna merah/hijau
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>

</html>