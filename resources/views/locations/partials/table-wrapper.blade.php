<div class="row">
    <!-- Tabel Offline -->
    <div class="col-12 col-md-6">
        <h4 class="text-danger">Data Offline</h4>
        <div class="table-responsive">
            <table class="table align-middle table-bordered table-striped">
                <thead class="text-center table-danger">
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
                    @foreach ($offlineLocations as $location)
                        <tr>
                            <td>{{ $location->name }}</td>
                            <td>{{ $location->type->name ?? '-' }}</td>
                            <td>{{ $location->latitude }}</td>
                            <td>{{ $location->longitude }}</td>
                            <td>{{ $location->ip_address ?? 'N/A' }}</td>
                            <td><span class="badge bg-danger">Offline</span></td>
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
    </div>

    <!-- Tabel Online -->
    <div class="col-12 col-md-6">
        <h4 class="text-success">Data Online</h4>
        <div class="table-responsive">
            <table class="table align-middle table-bordered table-striped">
                <thead class="text-center table-success">
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
                    @foreach ($onlineLocations as $location)
                        <tr>
                            <td>{{ $location->name }}</td>
                            <td>{{ $location->type->name ?? '-' }}</td>
                            <td>{{ $location->latitude }}</td>
                            <td>{{ $location->longitude }}</td>
                            <td>{{ $location->ip_address ?? 'N/A' }}</td>
                            <td><span class="badge bg-success">Online</span></td>
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
    </div>
</div>
