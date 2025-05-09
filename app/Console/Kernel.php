<?php

namespace App\Console;

use App\Models\Location;
use App\Models\DeviceUptime; // Pastikan Anda mengimpor model ini
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $locations = Location::all();
            foreach ($locations as $location) {
                $this->checkUptime($location);
            }
        })->everyMinute(); // Jalankan setiap 1 menit
    }

    /**
     * Cek uptime perangkat dan catat hasilnya.
     */
    private function checkUptime($location)
    {
        // Perintah ping untuk Windows atau Linux/MacOS
        $pingCommand = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            ? "ping -n 1 {$location->ip_address}"
            : "ping -c 1 {$location->ip_address}";

        // Jalankan ping dan periksa hasilnya
        $output = shell_exec($pingCommand);
        $status = (strpos($output, 'TTL') !== false) ? 'Online' : 'Offline';

        // Simpan hasil ke tabel device_uptimes
        DeviceUptime::create([
            'location_id' => $location->id,
            'status'      => $status,
            'checked_at'  => now(),
        ]);

        Log::info("Cek uptime untuk {$location->ip_address}: $status");
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
