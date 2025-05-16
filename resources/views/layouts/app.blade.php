<!DOCTYPE html>
<html lang="en">

<head>
    <title>Web GIS</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">



    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        #map {
            width: 100%;
            height: calc(100vh - 56px);
        }

        /* Responsif Sidebar */
        @media (min-width: 992px) {
            #sidebar {
                width: 280px;
                height: 100vh;
                position: static;
                /* Sidebar tetap di tempat di desktop */
                box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            }

            #sidebarToggle {
                display: none;
                /* Sembunyikan tombol di desktop */
            }
        }

        @media (max-width: 991px) {
            #sidebar {
                position: fixed;
                /* Sidebar mengambang di mobile */
                z-index: 1050;
            }
        }

        div.dataTables_filter {
            margin-bottom: 1rem;
            /* atau sesuai keinginan, contoh: 16px */
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="shadow-sm navbar navbar-light bg-light">
        <div class="container-fluid">
            <!-- Tombol Toggle untuk Mobile -->
            <button class="btn btn-outline-primary" type="button" id="sidebarToggle" data-bs-toggle="offcanvas"
                data-bs-target="#sidebar" aria-controls="sidebar">
                ☰ Menu
            </button>
            <span class="mx-auto navbar-brand fw-bold">Dashboard Monitoring Perangkat</span>
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="p-4 flex-grow-1">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    <!-- SweetAlert2 Notifications -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Error Alert
            @if ($errors->any())
                Swal.fire({
                    title: 'Kesalahan Input!',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            @endif

            // Success Alert
            @if (session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>

    @stack('scripts') <!-- Untuk script tambahan dari child view -->

</body>

</html>
