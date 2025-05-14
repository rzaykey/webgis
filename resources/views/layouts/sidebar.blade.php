<div id="sidebar" class="p-3 offcanvas-lg offcanvas-start bg-light" tabindex="-1">
    <!-- Logo -->
    <a href="/" class="mb-3 text-black d-flex align-items-center mb-md-0 me-md-auto text-decoration-none">
        <span class="fs-5 fw-bold">Dashboard</span>
    </a>
    <hr>

    <!-- Navigasi Utama -->
    <ul class="mb-auto nav nav-pills flex-column">

        <!-- Home Section -->
        <li class="nav-item">
            <a href="#homeSubmenu" class="text-black nav-link d-flex justify-content-between align-items-center"
                data-bs-toggle="collapse" aria-expanded="true" aria-controls="homeSubmenu">
                Home
                <i class="bi bi-chevron-down"></i>
            </a>
            <div class="collapse show" id="homeSubmenu">
                <ul class="nav flex-column ps-3">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="text-black nav-link">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('locations.index') }}" class="text-black nav-link">Data Perangkat</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('map') }}" class="text-black nav-link">Peta</a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Manajemen (Hanya Admin) -->
        @if (auth()->user()->isAdmin())
            <li class="nav-item">
                <a href="#managementSubmenu"
                    class="text-black nav-link d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse" aria-expanded="false" aria-controls="managementSubmenu">
                    Manajemen
                    <i class="bi bi-chevron-down"></i>
                </a>
                <div class="collapse" id="managementSubmenu">
                    <ul class="nav flex-column ps-3">
                        <li class="nav-item">
                            <a href="{{ route('sites.index') }}" class="text-black nav-link">Data Site</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('types.index') }}" class="text-black nav-link">Data Jenis Perangkat</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="text-black nav-link">Data Pengguna</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif

        <!-- Account Section -->
        <li class="nav-item">
            <a href="#accountSubmenu" class="text-black nav-link d-flex justify-content-between align-items-center"
                data-bs-toggle="collapse" aria-expanded="false" aria-controls="accountSubmenu">
                Account
                <i class="bi bi-chevron-down"></i>
            </a>
            <div class="collapse" id="accountSubmenu">
                <ul class="nav flex-column ps-3">
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}" class="text-black nav-link">Profile</a>
                    </li>
                    <li class="nav-item">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="text-black nav-link">Sign out</a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>

    <!-- Footer -->
    <hr>
    <div class="text-center text-muted small">
        &copy; {{ date('Y') }} Monitoring System
    </div>
</div>
