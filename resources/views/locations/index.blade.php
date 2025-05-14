@extends('layouts.app')

@section('content')
    <h2 class="mb-4 fw-bold">Data Perangkat CCTV & Access Point</h2>

    <!-- Tombol Tambah Lokasi -->
    <a href="{{ route('locations.create') }}" class="mb-4 btn btn-primary">Tambah Lokasi</a>

    <div class="row g-4">
        <div class="col-12">
            <!-- Tabel akan di-load secara dinamis -->
            <div id="table-container">
                @include('locations.partials.table-wrapper') <!-- Pastikan partial ini ada -->
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <style>
        /* Jarak antara search box dan tabel */
        div.dataTables_filter {
            margin-bottom: 10px;
        }
    </style>
@endpush

@push('scripts')
    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

    <!-- Plugin untuk sorting IP address -->
    <script>
        jQuery.extend(jQuery.fn.dataTableExt.oSort, {
            "ip-address-pre": function(a) {
                var m = a.split('.');
                if (m.length !== 4) return 0;
                return (parseInt(m[0]) << 24) | (parseInt(m[1]) << 16) | (parseInt(m[2]) << 8) | parseInt(m[3]);
            },
            "ip-address-asc": function(a, b) {
                return a - b;
            },
            "ip-address-desc": function(a, b) {
                return b - a;
            }
        });
    </script>

    <!-- Inisialisasi DataTables -->
    <script>
        function initTables() {
            $('table').each(function() {
                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        columnDefs: [{
                                targets: 4,
                                type: 'ip-address'
                            } // IP Address kolom ke-5 (0-indexed)
                        ],
                        order: [
                            [4, 'asc']
                        ],
                        responsive: true,
                        language: {
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            zeroRecords: "Tidak ada data ditemukan",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            infoEmpty: "Tidak ada data tersedia",
                            infoFiltered: "(disaring dari total _MAX_ data)"
                        }
                    });
                }
            });
        }

        function refreshTable() {
            fetch("{{ route('locations.partials.table') }}")
                .then(response => response.text())
                .then(html => {
                    $('#table-container').html(html);
                    initTables(); // re-init setelah reload
                })
                .catch(error => console.error('Gagal memuat data:', error));
        }

        // Inisialisasi saat pertama kali dan setiap 60 detik
        $(document).ready(function() {
            refreshTable();
            setInterval(refreshTable, 60000);
        });
    </script>
@endpush
