@extends('layouts.app')

@section('content')
    <h2>Edit Jenis Perangkat</h2>

    <form action="{{ route('types.update', $type) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Nama Jenis</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', $type->name) }}">
            @error('name')
                <div class="mt-1 text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Perbarui</button>
        <a href="{{ route('types.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
@endsection
