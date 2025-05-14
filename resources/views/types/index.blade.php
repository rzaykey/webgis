@extends('layouts.app')

@section('content')
    <h2>Data Jenis Perangkat</h2>

    <a href="{{ route('types.create') }}" class="mb-3 btn btn-primary">Tambah Jenis Perangkat</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($types as $type)
                <tr>
                    <td>{{ $type->name }}</td>
                    <td>
                        <a href="{{ route('types.edit', $type) }}" class="btn btn-sm btn-info">Edit</a>
                        <form action="{{ route('types.destroy', $type) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Yakin ingin menghapus?')"
                                class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Belum ada jenis perangkat</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
