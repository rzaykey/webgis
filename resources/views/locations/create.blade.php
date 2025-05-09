@extends('layouts.app')

@section('content')
    <h2>{{ isset($location) ? 'Edit' : 'Tambah' }} Lokasi</h2>
    <form action="{{ isset($location) ? route('locations.update', $location) : route('locations.store') }}" method="POST">
        @csrf
        @isset($location)
            @method('PUT')
        @endisset

        <div class="mb-3">
            <label for="name" class="form-label">Nama Perangkat</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ $location->name ?? '' }}"
                required>
        </div>

        <div class="mb-3">
            <label for="device_type" class="form-label">Tipe Perangkat</label>
            <input type="text" id="device_type" name="device_type" class="form-control"
                value="{{ $location->device_type ?? '' }}" required>
        </div>

        <div class="mb-5">
            <label for="ip_address" class="form-label">IP Address</label>
            <input type="text" id="ip_address" name="ip_address" class="form-control"
                value="{{ $location->ip_address ?? '' }}">
        </div>

        <div class="mb-3">
            <input type="text" id="search" placeholder="Cari Kota" class="form-control"
                style="border-radius: 8px; margin-bottom: 20px; width: 20%;">
        </div>

        <div id="map" style="height: 400px;"></div>

        <input type="hidden" id="latitude" name="latitude" value="{{ $location->latitude ?? '' }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ $location->longitude ?? '' }}">

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('locations.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Titik awal (fallback ke Jakarta jika tidak ada data)
            var centerLat = {{ $site->latitude ?? -6.2 }};
            var centerLng = {{ $site->longitude ?? 106.816666 }};

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

            // Event Listener: Pencarian kota (dengan Enter)
            const searchInput = document.getElementById('search');

            // Fungsi untuk mencari kota
            function searchCity(city) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(city)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lon = parseFloat(data[0].lon);

                            // Update marker dan posisi peta
                            marker.setLatLng([lat, lon]);
                            map.setView([lat, lon], 13);

                            // Set nilai latitude dan longitude ke form
                            document.getElementById('latitude').value = lat;
                            document.getElementById('longitude').value = lon;

                            alert(`Lokasi ditemukan: ${data[0].display_name}`);
                        } else {
                            alert('Lokasi tidak ditemukan.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal mencari kota. Periksa koneksi Anda.');
                    });
            }

            // Tambahkan event listener pada input untuk memicu pencarian
            searchInput.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    searchCity(searchInput.value);
                }
            });
        });
    </script>
@endsection
