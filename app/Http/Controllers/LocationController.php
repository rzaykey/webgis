<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Type;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Cache::remember('locations', 60, function () {
            return Location::with('type')->get()->map(function ($location) {
                $location->status = $this->checkDeviceStatus($location->ip_address);
                return $location;
            });
        });

        $onlineLocations = $locations->filter(fn($l) => $l->status === 'online');
        $offlineLocations = $locations->filter(fn($l) => $l->status === 'offline');

        return view('locations.index', compact('onlineLocations', 'offlineLocations'));
    }

    public function tablePartial()
    {
        $locations = Location::with('type')->get()->map(function ($location) {
            $location->status = $this->checkDeviceStatus($location->ip_address);
            return $location;
        });

        $onlineLocations = $locations->filter(fn($l) => $l->status === 'online');
        $offlineLocations = $locations->filter(fn($l) => $l->status === 'offline');

        return view('locations.partials.table-wrapper', compact('onlineLocations', 'offlineLocations'));
    }

    public function create()
    {
        $types = Type::all();
        return view('locations.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type_id' => 'required|exists:types,id',
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
        $types = Type::all();
        return view('locations.edit', compact('location', 'types'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required',
            'type_id' => 'required|exists:types,id',
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
        $locations = Cache::remember('locations', 60, function () {
            return Location::with('type')->get()->map(function ($location) {
                $location->status = $this->checkDeviceStatus($location->ip_address);
                return $location;
            });
        });

        $site = Cache::remember('site_center', 60, function () {
            return Site::first();
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
