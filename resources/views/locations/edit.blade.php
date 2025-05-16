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
            <input type="text" id="name" name="name" class="form-control"
                value="{{ old('name', $location->name ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label>Jenis Perangkat</label>
            <select name="type_id" class="form-control" required>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}"
                        {{ old('type_id', $location->type_id ?? '') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="ip_address" class="form-label">IP Address</label>
            <input type="text" id="ip_address" name="ip_address" class="form-control"
                value="{{ old('ip_address', $location->ip_address ?? '') }}">
        </div>

        <button type="button" id="btn-use-current-location" class="mb-3 btn btn-outline-primary">
            Gunakan Lokasi Saya
        </button>

        <div id="map" style="height: 400px;" class="mb-3"></div>

        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $location->latitude ?? '') }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $location->longitude ?? '') }}">

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
            let defaultLat = {{ $location->latitude ?? 'null' }};
            let defaultLng = {{ $location->longitude ?? 'null' }};

            let map, marker;

            function initMap(lat, lng) {
                map = L.map('map').setView([lat, lng], 17);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                marker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(map);

                marker.on('dragend', function(e) {
                    const pos = marker.getLatLng();
                    updateFormCoords(pos.lat, pos.lng);
                });

                map.on('click', function(e) {
                    marker.setLatLng(e.latlng);
                    updateFormCoords(e.latlng.lat, e.latlng.lng);
                });
            }

            function updateFormCoords(lat, lng) {
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
            }

            function moveToCurrentLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        let lat = position.coords.latitude;
                        let lng = position.coords.longitude;
                        if (!map) {
                            initMap(lat, lng);
                        } else {
                            map.setView([lat, lng], 17);
                            marker.setLatLng([lat, lng]);
                        }
                        updateFormCoords(lat, lng);
                    }, function() {
                        alert("Gagal mengambil lokasi Anda.");
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    });
                } else {
                    alert("Browser Anda tidak mendukung geolocation.");
                }
            }


            // Inisialisasi awal
            if (defaultLat !== null && defaultLng !== null) {
                initMap(defaultLat, defaultLng);
                updateFormCoords(defaultLat, defaultLng);
            } else {
                moveToCurrentLocation(); // default to current location if no location available
            }

            // Event handler tombol
            document.getElementById('btn-use-current-location').addEventListener('click', moveToCurrentLocation);
        });
    </script>
@endsection
