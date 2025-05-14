@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Header -->
        <div class="mb-4 text-center">
            <h2 class="fw-bold">Dashboard Monitoring Perangkat</h2>
        </div>

        <!-- Statistik Perangkat -->
        <div class="mb-4 row g-3">
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
                <div class="col-md-4">
                    <div class="card text-white {{ $stat['bg'] }} shadow-sm">
                        <div class="text-center card-body">
                            <h5 class="card-title">{{ $stat['title'] }}</h5>
                            <h2 class="display-5">{{ $stat['count'] }}</h2>
                            <p class="mb-0">{{ $stat['desc'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Chart Card -->
        <div class="mb-4 shadow-sm card">
            <div class="card-body">
                <h5 class="card-title">Waktu Online & Offline (Bulan Ini)</h5>
                <p>Total waktu online: <strong>{{ $uptimeFormatted }}</strong></p>
                <p>Total waktu offline: <strong>{{ $downtimeFormatted }}</strong></p>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="uptimeChart"></canvas>
                </div>
            </div>
        </div>

        <div class="mb-4 shadow-sm card">
            <div class="card-body">
                <h5 class="card-title">Total Perangkat Online vs Offline</h5>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="deviceStatusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="mb-4 shadow-sm card">
            <div class="card-body">
                <h5 class="card-title">Perangkat Berdasarkan Jenis dan Status</h5>
                <div class="chart-container" style="height: 400px;">
                    <canvas id="deviceTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const onlineDevices = {{ $onlineDevices ?? 0 }};
            const offlineDevices = {{ $offlineDevices ?? 0 }};
            const uptimeMinutes = {{ $uptimeInMinutes ?? 0 }};
            const downtimeMinutes = {{ $downtimeInMinutes ?? 0 }};
            const deviceTypes = @json($deviceTypes);

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

            // Uptime & Downtime Chart
            renderChart('uptimeChart', 'bar', {
                labels: ['Online (Menit)', 'Offline (Menit)'],
                datasets: [{
                    label: 'Durasi (Menit)',
                    data: [uptimeMinutes, downtimeMinutes],
                    backgroundColor: ['#4CAF50', '#F44336'],
                    borderRadius: 8
                }]
            });

            // Device Status Chart
            renderChart('deviceStatusChart', 'doughnut', {
                labels: ['Online', 'Offline'],
                datasets: [{
                    data: [onlineDevices, offlineDevices],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            });

            // Device by Type Chart
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
