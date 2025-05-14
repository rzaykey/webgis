<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TypeController extends Controller
{
    public function index()
    {
        $types = Cache::remember('types', 60, function () {
            return Type::all();
        });

        return view('types.index', compact('types'));
    }

    public function create()
    {
        return view('types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Type::create($request->only('name'));
        Cache::forget('types');

        return redirect()->route('types.index')->with('success', 'Jenis Perangkat berhasil ditambahkan');
    }

    public function edit(Type $type)
    {
        return view('types.edit', compact('type'));
    }

    public function update(Request $request, Type $type)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $type->update($request->only('name'));
        Cache::forget('types');

        return redirect()->route('types.index')->with('success', 'Jenis Perangkat berhasil diperbarui');
    }

    public function destroy(Type $type)
    {
        $type->delete();
        Cache::forget('types');

        return redirect()->route('types.index')->with('success', 'Jenis Perangkat berhasil dihapus');
    }
}
