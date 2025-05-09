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

        <div class="mb-3">
            <label for="ip_address" class="form-label">IP Address</label>
            <input type="text" id="ip_address" name="ip_address" class="form-control"
                value="{{ $location->ip_address ?? '' }}">
        </div>

        <div id="map" style="height: 400px;"></div>

        <input type="hidden" id="latitude" name="latitude" value="{{ $location->latitude ?? '' }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ $location->longitude ?? '' }}">

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('locations.index') }}" class="btn btn-secondary">Kembali</a>
    </form>

    <script>
        var centerLat = {{ $location->latitude ?? -6.2 }};
        var centerLng = {{ $location->longitude ?? 106.816666 }};

        var map = L.map('map').setView([centerLat, centerLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker = L.marker([centerLat, centerLng]).addTo(map);

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            document.getElementById('latitude').value = e.latlng.lat;
            document.getElementById('longitude').value = e.latlng.lng;
        });
    </script>
@endsection
