<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/custom-login.css') }}">
    <title>Login</title>
</head>
<body>
    <div class="bg-overlay d-flex justify-content-center align-items-center min-vh-100">
        <div class="card login-card shadow-lg">
            <div class="card-body">
                <div class="text-center mb-3">
                    <img src="{{ asset('image/Lambang_Kabupaten_Blora.gif') }}" alt="Logo" style="width: 130px;"">
                </div>

                <h5 class="text-center text-white mb-4" style="font-weight: 500;">Sistem Surat Dan Agenda Digital</h5>

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <input type="text" class="form-control custom-input py-2" name="NIP" placeholder="NIP" value="{{ old('NIP') }}" required autocomplete="off">
                        @error('NIP')
                            <div class="text-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <input type="password" class="form-control custom-input py-2" name="password" placeholder="Password" required>
                        @error('password')
                            <div class="text-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-center mt-2">
                        <button type="submit" class="btn btn-success text-white">Login</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</body>
</html>