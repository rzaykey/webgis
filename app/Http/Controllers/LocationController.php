<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\DeviceUptime;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Cache::remember('locations', 60, function () {
            return Location::all()->map(function ($location) {
                $location->status = $this->checkDeviceStatus($location->ip_address);
                return $location;
            });
        });

        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'device_type' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'ip_address' => 'nullable|ip',
        ]);

        Location::create($request->all());
        Cache::forget('locations');
        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil ditambahkan');
    }

    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required',
            'device_type' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'ip_address' => 'nullable|ip',
        ]);

        $location->update($request->all());
        Cache::forget('locations');
        return redirect()->route('locations.index');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        Cache::forget('locations');
        return redirect()->route('locations.index');
    }

    public function map()
    {
        // Ambil semua lokasi dan cache selama 60 menit
        $locations = Cache::remember('locations', 60, function () {
            return Location::all()->map(function ($location) {
                $location->status = $this->checkDeviceStatus($location->ip_address);
                return $location;
            });
        });

        // Ambil data pertama dari tabel 'sites' sebagai pusat peta
        $site = Cache::remember('site_center', 60, function () {
            return Site::first(); // Bisa diganti dengan kondisi tertentu
        });

        return view('map', compact('locations', 'site'));
    }

    private function checkDeviceStatus($ip)
    {
        if (!$ip) return 'unknown';

        $pingCommand = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            ? "ping -n 1 $ip"
            : "ping -c 1 $ip";

        exec($pingCommand, $output, $resultCode);

        return ($resultCode === 0) ? 'online' : 'offline';
    }
}
