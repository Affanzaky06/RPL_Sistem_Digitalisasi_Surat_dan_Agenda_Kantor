<header class="d-flex flex-wrap justify-content-center py-3 border-bottom flex-shrink-0 bg-success"> <a href="/"
            class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none    ">

            <svg class="bi me-2" width="40" height="32" aria-hidden="true">
                <use xlink:href="#bootstrap"></use>
                <img src="{{ asset('image/Lambang_Kabupaten_Blora.gif') }}" alt="Logo Aplikasi" width="35"">
            </svg> <span class="px-3 fs-5 fw-bold fc-white text-white">{{ $slot }}</span> </a>
        <div class="dropdown px-4"> <a href="#"
                class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle"
                data-bs-toggle="dropdown" aria-expanded="false"> <img src="https://ilmutanah.upnyk.ac.id/public/assets/dosen/thumb/9204738615.png" alt="" width="32"
                    height="32" class="rounded-circle me-2"> <strong class="text-white">{{ auth()->user()->nama }}</strong> </a>
            <ul class="dropdown-menu text-small shadow">
                <li><a class="dropdown-item" href="#">New project...</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Sign out</a></li>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </ul>
        </div>

    </header>