@props(['role' => 'frontliner'])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <title>Dasboard</title>
</head>


<body class="vh-100 d-flex flex-column overflow-hidden ">

    <x-header>
        Sistem Agenda Digital - {{ $title }}
    </x-header>

    <div class="d-flex flex-grow-1 overflow-hidden">
        
        @if($role === 'Kepala' || $role === 'Kabid')
            <x-navbar-k-p :role="$role"></x-navbar-k-p>
        @else
            <x-navbar-fr></x-navbar-fr>
        @endif

        <main class="flex-grow-1 p-4 overflow-auto">
            <h2>{{ $slot }}</h2>
            <p>Ini adalah area tempat konten utama berada. Sidebar dan Header akan tetap diam di posisinya.</p>
            <p>Coba tambahkan banyak teks atau elemen di sini untuk melihat bagaimana scrollbar hanya akan muncul di
                area putih ini, tanpa merusak layout sidebar dan header.</p>
        </main>

    </div>

</body>

</html>