@extends('layouts.app')

@section('content')
    <h2 class="mb-4 fw-bold">Data Perangkat CCTV & Access Point</h2>

    <!-- Tombol Tambah Lokasi -->
    <a href="{{ route('locations.create') }}" class="mb-4 btn btn-primary">Tambah Lokasi</a>

    <!-- Form import -->
    <form action="{{ route('locations.import') }}" method="POST" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="input-group" style="max-width: 400px;">
            <input type="file" name="file" class="form-control" required>
            <button type="submit" class="btn btn-success">Import Excel</button>
        </div>
    </form>

    <!-- Container untuk partial tabel -->
    <div id="table-container">
        @include('locations.partials.table-wrapper')
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" />
    <style>
        div.dataTables_filter {
            margin-bottom: 10px;
        }
    </style>
@endpush

@push('scripts')
    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

    <script>
        jQuery.extend(jQuery.fn.dataTableExt.oSort, {
            "ip-address-pre": function(a) {
                var m = a.split('.');
                if (m.length !== 4) return 0;
                return (parseInt(m[0], 10) << 24) |
                    (parseInt(m[1], 10) << 16) |
                    (parseInt(m[2], 10) << 8) |
                    parseInt(m[3], 10);
            },
            "ip-address-asc": function(a, b) {
                return a - b;
            },
            "ip-address-desc": function(a, b) {
                return b - a;
            }
        });

        function initTables() {
            $('#offlineTable').DataTable({
                columnDefs: [{
                    targets: 4,
                    type: 'ip-address'
                }],
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

            $('#onlineTable').DataTable({
                columnDefs: [{
                    targets: 4,
                    type: 'ip-address'
                }],
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

        $(document).ready(function() {
            initTables();

            // Contoh reload table setiap 60 detik via AJAX (kalau ada route untuk partial)
            // setInterval(function() {
            //     $.ajax({
            //         url: "{{ route('locations.partials.table') }}",
            //         method: 'GET',
            //         success: function(data) {
            //             $('#table-container').html(data);
            //             initTables();
            //         }
            //     });
            // }, 60000);
        });
    </script>
@endpush
