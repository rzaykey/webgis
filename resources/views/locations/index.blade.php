@extends('layouts.app')

@section('content')
    <h2>Data CCTV & Access Point</h2>

    <!-- Tombol Tambah Lokasi -->
    <a href="{{ route('locations.create') }}" class="mb-3 btn btn-primary">Tambah Lokasi</a>

    <!-- Tabel Responsif -->
    <div class="table-responsive">
        <table class="table align-middle table-bordered table-striped">
            <thead class="text-center table-primary">
                <tr>
                    <th>Nama</th>
                    <th>Jenis</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>IP Address</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($locations as $location)
                    <tr>
                        <td>{{ $location->name }}</td>
                        <td>{{ $location->device_type }}</td>
                        <td>{{ $location->latitude }}</td>
                        <td>{{ $location->longitude }}</td>
                        <td>{{ $location->ip_address ?? 'N/A' }}</td>
                        <td>
                            @if ($location->status == 'online')
                                <span class="badge bg-success">Online</span>
                            @else
                                <span class="badge bg-danger">Offline</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('locations.edit', $location) }}" class="btn btn-sm btn-info">Edit</a>

                            <form action="{{ route('locations.destroy', $location) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus?');">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
