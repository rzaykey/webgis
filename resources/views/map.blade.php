@extends('layouts.app')

@section('content')
    <h2 class="mb-4 text-center">Tampilan Peta Lokasi CCTV dan Access Point</h2>

    <!-- Input Pencarian -->
    <div class="mb-3 d-flex justify-content-center">
        <input type="text" id="search" placeholder="Cari Kota" class="form-control w-100 w-md-50"
            style="max-width: 400px; border-radius: 8px;">
    </div>

    <!-- Kontainer Peta -->
    <div id="map" style="width: 100%; height: 70vh; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></div>

    <!-- Leaflet JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pastikan data diambil secara benar, fallback jika undefined
            var locations = {!! json_encode($locations ?? []) !!};
            var site = {!! json_encode($site ?? ['latitude' => -6.2, 'longitude' => 106.816666]) !!};

            // Menentukan pusat peta (default ke Jakarta jika tidak ada koordinat valid)
            var center = [
                parseFloat(site.latitude) || -6.2,
                parseFloat(site.longitude) || 106.816666
            ];

            // Inisialisasi peta
            var map = L.map('map').setView(center, 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Array untuk menyimpan bounds (agar peta bisa auto-zoom ke semua marker)
            var bounds = [];

            // Tambahkan marker untuk setiap lokasi
            locations.forEach(function(loc) {
                // URL ikon kustom berdasarkan tipe perangkat dan status
                let iconUrl =
                    `/images/${(loc.device_type || 'default').toLowerCase()}-${(loc.status || 'unknown').toLowerCase()}.png`;

                // Konfigurasi ikon custom
                let customIcon = L.icon({
                    iconUrl: iconUrl,
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });

                // Tambahkan marker dengan popup informasi
                let marker = L.marker([loc.latitude, loc.longitude], {
                        icon: customIcon
                    })
                    .addTo(map)
                    .bindPopup(`
                        <strong>${loc.name ?? 'Tidak Ada Nama'}</strong><br>
                        Tipe: ${loc.device_type ?? 'Tidak Diketahui'}<br>
                        Status: ${loc.status ?? 'Tidak Ada Status'}<br>
                        IP: ${loc.ip_address ?? 'N/A'}
                    `);

                bounds.push([loc.latitude, loc.longitude]);
            });

            // Auto-zoom ke semua marker
            if (bounds.length > 0) {
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });
            }

            // Fitur Pencarian Lokasi (Kota)
            const searchInput = document.getElementById('search');
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchLocation(searchInput.value);
                }
            });

            // Fungsi pencarian lokasi menggunakan OpenStreetMap Nominatim API
            function searchLocation(query) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            let result = data[0];
                            map.setView([result.lat, result.lon], 14);
                        } else {
                            alert('Lokasi tidak ditemukan.');
                        }
                    })
                    .catch(() => alert('Terjadi kesalahan dalam pencarian.'));
            }
        });
    </script>
@endsection
