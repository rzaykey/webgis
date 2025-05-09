@extends('layouts.app')

@section('content')
    <h2>{{ isset($site) ? 'Edit' : 'Tambah' }} Lokasi</h2>

    <form action="{{ isset($site) ? route('sites.update', $site) : route('sites.store') }}" method="POST">
        @csrf
        @isset($site)
            @method('PUT')
        @endisset

        <div class="mb-3">
            <label for="name" class="form-label">Nama Site</label>
            <input style="border-radius: 8px; margin-bottom: 20px; width: 20%;" type="text" id="name" name="name"
                class="form-control" value="{{ $site->name ?? '' }}" required>
        </div>

        <!-- Input Pencarian Kota -->
        <div class="mb-3">
            <input type="text" id="search" placeholder="Cari Kota" class="form-control"
                style="border-radius: 8px; margin-bottom: 20px; width: 20%;">
        </div>
        <!-- Peta -->
        <div id="map" style="height: 400px;"></div>

        <!-- Input Latitude & Longitude (Hidden) -->
        <input type="hidden" id="latitude" name="latitude" value="{{ $site->latitude ?? '' }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ $site->longitude ?? '' }}">

        <button type="submit" class="mt-3 btn btn-success">Simpan</button>
        <a href="{{ route('sites.index') }}" class="mt-3 btn btn-secondary">Kembali</a>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil koordinat (gunakan fallback jika kosong)
            var centerLat = parseFloat("{{ $site->latitude ?? -6.2 }}");
            var centerLng = parseFloat("{{ $site->longitude ?? 106.816666 }}");

            // Pastikan koordinat valid (fallback jika null atau NaN)
            if (isNaN(centerLat) || isNaN(centerLng)) {
                centerLat = -6.2; // Jakarta sebagai default
                centerLng = 106.816666;
            }

            // Inisialisasi peta
            var map = L.map('map').setView([centerLat, centerLng], 13);

            // Tambahkan tile layer dari OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Tambahkan marker di posisi awal
            var marker = L.marker([centerLat, centerLng], {
                draggable: true
            }).addTo(map);

            // Update koordinat di input hidden jika marker digeser
            marker.on('dragend', function(e) {
                var position = marker.getLatLng();
                document.getElementById('latitude').value = position.lat;
                document.getElementById('longitude').value = position.lng;
            });

            // Update koordinat saat peta diklik
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                document.getElementById('latitude').value = e.latlng.lat;
                document.getElementById('longitude').value = e.latlng.lng;
            });

            // Event Listener: Pencarian kota
            const searchInput = document.getElementById('search');

            function searchCity(city) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(city)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lon = parseFloat(data[0].lon);

                            // Validasi hasil pencarian
                            if (!isNaN(lat) && !isNaN(lon)) {
                                marker.setLatLng([lat, lon]);
                                map.setView([lat, lon], 13);
                                document.getElementById('latitude').value = lat;
                                document.getElementById('longitude').value = lon;
                                alert(`Lokasi ditemukan: ${data[0].display_name}`);
                            } else {
                                alert('Koordinat tidak valid.');
                            }
                        } else {
                            alert('Lokasi tidak ditemukan.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal mencari kota. Periksa koneksi Anda.');
                    });
            }

            // Tambahkan event listener pada input
            searchInput.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Mencegah form submit
                    searchCity(searchInput.value);
                }
            });
        });
    </script>
@endsection
