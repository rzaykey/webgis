@extends('layouts.app')

@section('content')
    <h2>Tambah Lokasi</h2>

    <form action="{{ route('locations.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nama Perangkat</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label>Jenis Perangkat</label>
            <select name="type_id" class="form-control" required>
                <option value="">-- Pilih Jenis --</option>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-5">
            <label for="ip_address" class="form-label">IP Address</label>
            <input type="text" id="ip_address" name="ip_address" class="form-control" value="{{ old('ip_address') }}">
        </div>

        <div class="mb-3 d-flex" style="gap: 10px; flex-wrap: wrap; align-items: center;">
            <input type="text" id="search" placeholder="Cari Kota" class="form-control"
                style="max-width: 300px; border-radius: 8px;">
            <button type="button" id="btnSearchCity" class="btn btn-primary">Cari</button>
            <button type="button" id="btnUseMyLocation" class="btn btn-info">Gunakan Lokasi Saya</button>
        </div>

        <div id="map" style="height: 400px; margin-bottom: 20px;"></div>

        <input type="hidden" id="latitude" name="latitude"
            value="{{ old('latitude', $defaultLocation->latitude ?? '') }}">
        <input type="hidden" id="longitude" name="longitude"
            value="{{ old('longitude', $defaultLocation->longitude ?? '') }}">

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('locations.index') }}" class="btn btn-secondary">Kembali</a>
    </form>

    {{-- Leaflet.js CSS & JS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-o9N1j7kQWd+fU2zWrjFiobWI33p3RaG3aS2fzjGv4cQ=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-o9N1j7kQWd+fU2zWrjFiobWI33p3RaG3aS2fzjGv4cQ=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Koordinat awal default (dari backend atau fallback Jakarta)
            var initialLat = parseFloat(document.getElementById('latitude').value) || -6.200000;
            var initialLng = parseFloat(document.getElementById('longitude').value) || 106.816666;

            var map = L.map('map').setView([initialLat, initialLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            var marker = L.marker([initialLat, initialLng], {
                draggable: true
            }).addTo(map);

            // Update koordinat input saat marker drag
            marker.on('dragend', function() {
                var pos = marker.getLatLng();
                document.getElementById('latitude').value = pos.lat;
                document.getElementById('longitude').value = pos.lng;
            });

            // Update koordinat saat klik peta
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                document.getElementById('latitude').value = e.latlng.lat;
                document.getElementById('longitude').value = e.latlng.lng;
            });

            // Tombol "Gunakan Lokasi Saya" (high accuracy)
            document.getElementById('btnUseMyLocation').addEventListener('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;

                        map.setView([lat, lng], 16);
                        marker.setLatLng([lat, lng]);
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                    }, function(err) {
                        alert('Gagal mendapatkan lokasi. Pastikan izin lokasi diaktifkan.');
                    }, {
                        enableHighAccuracy: true,
                        timeout: 7000,
                        maximumAge: 0
                    });
                } else {
                    alert('Geolocation tidak didukung browser Anda.');
                }
            });

            // Fungsi pencarian kota pakai Nominatim OpenStreetMap
            function searchCity(city) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(city)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            var lat = parseFloat(data[0].lat);
                            var lon = parseFloat(data[0].lon);

                            map.setView([lat, lon], 13);
                            marker.setLatLng([lat, lon]);
                            document.getElementById('latitude').value = lat;
                            document.getElementById('longitude').value = lon;
                        } else {
                            alert('Lokasi tidak ditemukan.');
                        }
                    }).catch(() => {
                        alert('Gagal mencari lokasi. Periksa koneksi internet Anda.');
                    });
            }

            // Tombol cari kota
            document.getElementById('btnSearchCity').addEventListener('click', function() {
                var query = document.getElementById('search').value.trim();
                if (query) {
                    searchCity(query);
                }
            });

            // Enter di kolom cari juga trigger pencarian
            document.getElementById('search').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    var query = this.value.trim();
                    if (query) {
                        searchCity(query);
                    }
                }
            });
        });
    </script>
@endsection
