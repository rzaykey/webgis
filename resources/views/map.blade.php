@extends('layouts.app')

@section('content')
    <h2 class="mb-4 text-center">Tampilan Peta Lokasi CCTV dan Access Point</h2>

    <!-- Input Pencarian -->
    <div class="mb-3 d-flex justify-content-center">
        <label for="search" class="visually-hidden">Cari Lokasi</label>
        <input type="text" id="search" placeholder="Cari Kota (tekan Enter)" class="form-control w-100 w-md-50"
            style="max-width: 400px; border-radius: 8px;">
    </div>

    <!-- Kontainer Peta -->
    <div id="map" style="width: 100%; height: 70vh; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></div>

    <!-- CSS untuk Custom Marker -->
    <style>
        .custom-marker {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <!-- Leaflet JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var locations = {!! json_encode($locations ?? []) !!};
            var site = {!! json_encode($site ?? ['latitude' => -6.2, 'longitude' => 106.816666]) !!};

            var center = [
                isFinite(parseFloat(site.latitude)) ? parseFloat(site.latitude) : -6.2,
                isFinite(parseFloat(site.longitude)) ? parseFloat(site.longitude) : 106.816666
            ];

            var map = L.map('map').setView(center, 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            var bounds = [];

            locations.forEach(function(loc) {
                if (!loc.latitude || !loc.longitude) return;

                // Warna berdasarkan status
                let iconColor = (loc.status || '').toLowerCase() === 'online' ? 'green' : 'red';

                let customIcon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="
                        background-color: ${iconColor};
                        width: 16px;
                        height: 16px;
                        border-radius: 50%;
                        border: 2px solid white;
                        box-shadow: 0 0 4px rgba(0,0,0,0.5);
                    "></div>`,
                    iconSize: [16, 16],
                    iconAnchor: [8, 8],
                    popupAnchor: [0, -8]
                });

                let marker = L.marker([loc.latitude, loc.longitude], {
                        icon: customIcon
                    })
                    .addTo(map)
                    .bindPopup(`
                        <strong>${escapeHtml(loc.name ?? 'Tidak Ada Nama')}</strong><br>
                        Tipe: ${escapeHtml(loc.device_type ?? 'Tidak Diketahui')}<br>
                        Status: ${escapeHtml(loc.status ?? 'Tidak Ada Status')}<br>
                        IP: ${escapeHtml(loc.ip_address ?? 'N/A')}
                    `);

                bounds.push([loc.latitude, loc.longitude]);
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });
            }

            const searchInput = document.getElementById('search');
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchLocation(searchInput.value);
                }
            });

            function searchLocation(query) {
                searchInput.disabled = true;

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
                    .catch(() => alert('Terjadi kesalahan dalam pencarian.'))
                    .finally(() => {
                        searchInput.disabled = false;
                    });
            }

            // Fungsi untuk escape HTML
            function escapeHtml(text) {
                return String(text).replace(/[&<>"']/g, function(m) {
                    return ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    })[m];
                });
            }
        });
    </script>
@endsection
