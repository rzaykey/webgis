<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\DeviceUptime;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil semua lokasi dan update status perangkat
        $locations = Location::all()->map(function ($location) {
            $location->status = $this->checkDeviceStatus($location->ip_address);
            return $location;
        });

        // Ambil data uptime/downtime bulan ini
        $monthlyUptimes = DeviceUptime::whereBetween('checked_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ])->orderBy('checked_at')->get();

        // Hitung durasi uptime dan downtime
        [$uptimeInMinutes, $downtimeInMinutes] = $this->calculateDurations($monthlyUptimes);

        // Konversi ke jam atau menit
        $uptimeFormatted = $this->formatDuration($uptimeInMinutes);
        $downtimeFormatted = $this->formatDuration($downtimeInMinutes);

        // Hitung total perangkat, online, dan offline
        $totalDevices = $locations->count();
        $onlineDevices = $locations->where('status', 'Online')->count();
        $offlineDevices = $locations->where('status', 'Offline')->count();

        // Hitung jumlah perangkat berdasarkan jenis (dinamis dari database)
        $deviceTypes = $locations->groupBy('device_type')->map(function ($group) {
            return [
                'online' => $group->where('status', 'Online')->count(),
                'offline' => $group->where('status', 'Offline')->count(),
            ];
        });

        $uptimeByLocation = $monthlyUptimes->groupBy('location_id')->map(function ($records) {
            [$onlineDuration, $offlineDuration] = $this->calculateDurations($records);

            return [
                'online' => $this->formatDuration($onlineDuration),
                'offline' => $this->formatDuration($offlineDuration),
            ];
        });


        return view('index', compact(
            'totalDevices',
            'onlineDevices',
            'offlineDevices',
            'deviceTypes',
            'uptimeFormatted',
            'downtimeFormatted',
            'uptimeInMinutes',
            'downtimeInMinutes',
            'locations',
            'uptimeByLocation'
        ));
    }

    // Fungsi memeriksa status perangkat menggunakan ping
    private function checkDeviceStatus($ip)
    {
        if (!$ip) return 'Unknown';

        $pingCommand = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            ? "ping -n 1 $ip"
            : "ping -c 1 $ip";

        $output = shell_exec($pingCommand);

        return (strpos($output, 'TTL') !== false) ? 'Online' : 'Offline';
    }

    // Fungsi menghitung durasi uptime dan downtime
    private function calculateDurations($records)
    {
        $totalOnline = 0;
        $totalOffline = 0;

        foreach ($records as $index => $record) {
            // Konversi checked_at menjadi objek Carbon jika belum
            $currentTime = Carbon::parse($record->checked_at);

            // Ambil record berikutnya
            $nextRecord = $records->get($index + 1);

            if ($nextRecord) {
                $nextTime = Carbon::parse($nextRecord->checked_at);

                // Hitung selisih dalam menit
                $duration = $currentTime->diffInMinutes($nextTime);

                // Tambahkan ke total durasi sesuai status
                if ($record->status == 'Online') {
                    $totalOnline += $duration;
                } else {
                    $totalOffline += $duration;
                }
            }
        }

        return [$totalOnline, $totalOffline];
    }


    // Fungsi mengonversi menit ke format jam atau menit
    private function formatDuration($minutes)
    {
        if ($minutes < 60) {
            return $minutes . ' menit';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        return $hours . ' jam ' . ($remainingMinutes > 0 ? $remainingMinutes . ' menit' : '');
    }
}
