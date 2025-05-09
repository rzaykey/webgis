@extends('layouts.app')

@section('content')
    <div class="mt-4 container-fluid">
        <!-- Header -->
        <h2 class="text-center fw-bold">Dashboard Monitoring Perangkat</h2>

        <!-- Statistik Perangkat -->
        <div class="row g-3">
            @php
                $stats = [
                    [
                        'title' => 'Perangkat Online',
                        'count' => $onlineDevices,
                        'bg' => 'bg-success',
                        'desc' => 'Perangkat aktif saat ini',
                    ],
                    [
                        'title' => 'Perangkat Offline',
                        'count' => $offlineDevices,
                        'bg' => 'bg-danger',
                        'desc' => 'Perangkat tidak aktif',
                    ],
                    [
                        'title' => 'Total Perangkat',
                        'count' => $totalDevices,
                        'bg' => 'bg-primary',
                        'desc' => 'Jumlah keseluruhan perangkat',
                    ],
                ];
            @endphp

            @foreach ($stats as $stat)
                <!-- Setiap card akan memenuhi lebar penuh di semua perangkat -->
                <div class="col-12">
                    <div class="card text-white shadow-sm {{ $stat['bg'] }}">
                        <div class="text-center card-body">
                            <h5 class="card-title">{{ $stat['title'] }}</h5>
                            <h2 class="display-5">{{ $stat['count'] }}</h2>
                            <p class="mb-0">{{ $stat['desc'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Waktu Online dan Diagram Status -->
        <div class="mt-4 row g-3">
            <!-- Waktu Online -->
            <div class="col-12">
                <div class="shadow-sm card">
                    <div class="card-body">
                        <h5 class="card-title">Waktu Online & Offline (Bulan Ini)</h5>
                        <p>Total waktu online: <strong>{{ $uptimeFormatted }}</strong></p>
                        <p>Total waktu offline: <strong>{{ $downtimeFormatted }}</strong></p>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="uptimeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagram Persentase -->
            <div class="col-12">
                <div class="shadow-sm card">
                    <div class="card-body">
                        <h5 class="card-title">Total Perangkat Online vs Offline</h5>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="deviceStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Perangkat Berdasarkan Jenis -->
        <div class="mt-4 row g-3">
            <div class="col-12">
                <div class="shadow-sm card">
                    <div class="card-body">
                        <h5 class="card-title">Perangkat Berdasarkan Jenis dan Status</h5>
                        <div class="chart-container" style="height: 400px;">
                            <canvas id="deviceTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const onlineDevices = {{ $onlineDevices ?? 0 }};
            const offlineDevices = {{ $offlineDevices ?? 0 }};
            const uptimeMinutes = {{ $uptimeInMinutes ?? 0 }};
            const downtimeMinutes = {{ $downtimeInMinutes ?? 0 }};
            const deviceTypes = @json($deviceTypes);

            // Fungsi Inisialisasi Chart
            function renderChart(id, type, data) {
                new Chart(document.getElementById(id), {
                    type: type,
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // 1. Chart Uptime dan Downtime (Bar Chart)
            renderChart('uptimeChart', 'bar', {
                labels: ['Online (Menit)', 'Offline (Menit)'],
                datasets: [{
                    label: 'Durasi (Menit)',
                    data: [uptimeMinutes, downtimeMinutes],
                    backgroundColor: ['#4CAF50', '#F44336'],
                    borderRadius: 8
                }]
            });

            // 2. Chart Status Perangkat (Doughnut Chart)
            renderChart('deviceStatusChart', 'doughnut', {
                labels: ['Online', 'Offline'],
                datasets: [{
                    data: [onlineDevices, offlineDevices],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            });

            // 3. Chart Perangkat Berdasarkan Jenis (Bar Chart)
            const deviceLabels = Object.keys(deviceTypes);
            const deviceDataOnline = deviceLabels.map(type => deviceTypes[type].online ?? 0);
            const deviceDataOffline = deviceLabels.map(type => deviceTypes[type].offline ?? 0);

            renderChart('deviceTypeChart', 'bar', {
                labels: deviceLabels,
                datasets: [{
                        label: 'Online',
                        backgroundColor: '#28a745',
                        data: deviceDataOnline
                    },
                    {
                        label: 'Offline',
                        backgroundColor: '#dc3545',
                        data: deviceDataOffline
                    }
                ]
            });
        });
    </script>
@endsection
