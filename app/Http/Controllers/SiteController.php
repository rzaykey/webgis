<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteController extends Controller
{
    /**
     * Tampilkan daftar lokasi (site).
     */
    public function index()
    {
        $sites = Cache::remember('sites', 60, function () {
            return Site::all(); // Hanya mengambil data tanpa memeriksa status perangkat
        });

        return view('sites.index', compact('sites'));
    }

    /**
     * Tampilkan form untuk membuat lokasi baru.
     */
    public function create()
    {
        return view('sites.create');
    }

    /**
     * Simpan lokasi baru ke dalam database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Site::create($request->all());
        Cache::forget('sites'); // Hapus cache agar data terbaru muncul
        return redirect()->route('sites.index')->with('success', 'Lokasi berhasil ditambahkan');
    }

    /**
     * Tampilkan form untuk mengedit lokasi.
     */
    public function edit(Site $site)
    {
        return view('sites.edit', compact('site'));
    }

    /**
     * Perbarui lokasi di dalam database.
     */
    public function update(Request $request, Site $site)
    {
        $request->validate([
            'name' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $site->update($request->all());
        Cache::forget('sites'); // Hapus cache agar data terbaru muncul
        return redirect()->route('sites.index')->with('success', 'Lokasi berhasil diperbarui');
    }

    /**
     * Hapus lokasi dari database.
     */
    public function destroy(Site $site)
    {
        $site->delete();
        Cache::forget('sites'); // Hapus cache agar data terbaru muncul
        return redirect()->route('sites.index')->with('success', 'Lokasi berhasil dihapus');
    }

    /**
     * Tampilkan peta dengan lokasi.
     */
    public function map()
    {
        $sites = Cache::remember('sites', 60, function () {
            return Site::all(); // Hanya mengambil data tanpa memeriksa status perangkat
        });

        return view('map', compact('sites'));
    }
}
