@extends('layouts.app')
@section('content')
    <h2>Data Site</h2>
    @if ($sites->isEmpty())
        <a href="{{ route('sites.create') }}" class="btn btn-primary">Tambah Site</a>
    @endif
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Latitude</th>
                <th>Longitude</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sites as $site)
                <tr>
                    <td>{{ $site->name }}</td>
                    <td>{{ $site->latitude }}</td>
                    <td>{{ $site->longitude }}</td>
                    <td>
                        <a href="{{ route('sites.edit', $site) }}" class="btn btn-sm btn-info">Edit</a>
                        <form action="{{ route('sites.destroy', $site) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
