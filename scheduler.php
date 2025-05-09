<?php

while (true) {
    echo "[" . date('Y-m-d H:i:s') . "] Menjalankan Schedule...\n";
    exec('php artisan schedule:run');
    sleep(60); // Jalankan setiap 1 menit
}
