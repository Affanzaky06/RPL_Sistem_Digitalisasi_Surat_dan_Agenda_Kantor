@props(['role' => 'frontliner'])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="{{ asset('Lambang_Kabupaten_Blora copy.png') }}">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <title>Sistem Agenda Digital</title>
</head>


<body class="vh-100 d-flex flex-column overflow-hidden ">
            
    <x-header>
        Sistem Agenda Digital - {{ $title }}
    </x-header>

    <div class="d-flex flex-grow-1 overflow-hidden">

        @if ($role === 'Kepala' || $role === 'Kabid' || $role === 'Subkoor')
            <x-navbar-k-p :role="$role" />
        @elseif($role === 'Staff')
            <x-navbar-staff />
        @elseif($role === 'Sekretaris')
            <x-navbar-sekretaris :role="$role"/>
        @elseif($role === 'Kepegawaian')
            <x-navbar-kpeg />
        @else
            <x-navbar-fr />
        @endif

        <main class="flex-grow-1 px-4 py-3 overflow-auto">
            {{ $slot }}

        </main>

    </div>

    <!-- Script Notifikasi Real-time & Desktop -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Minta izin notifikasi desktop jika belum
            if (Notification.permission !== "granted" && Notification.permission !== "denied") {
                Notification.requestPermission();
            }

            let lastCount = 0;
            const navBadge = document.getElementById('nav-notif-badge');

            function fetchNotifications() {
                fetch('{{ route("notifications.unread") }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    // Update Sidebar Badge if exists
                    if (navBadge) {
                        if (data.count > 0) {
                            navBadge.style.display = 'inline-block';
                            navBadge.innerText = data.count > 99 ? '99+' : data.count;
                        } else {
                            navBadge.style.display = 'none';
                        }
                    }

                    // Tembak Notifikasi Desktop jika ada penambahan count
                    if (data.count > lastCount && lastCount !== 0) {
                        if (data.notifications.length > 0) {
                            const latest = data.notifications[0];
                            if (Notification.permission === "granted") {
                                new Notification(latest.title, {
                                    body: latest.body,
                                    icon: '{{ asset("Lambang_Kabupaten_Blora copy.png") }}'
                                });
                            }
                        }
                    }
                    
                    if (lastCount === 0 || data.count !== lastCount) {
                        lastCount = data.count;
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error));
            }

            // Fetch pertama kali, lalu setiap 3 detik
            fetchNotifications();
            setInterval(fetchNotifications, 3000);
        });
    </script>
</body>
</html>
