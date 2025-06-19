@extends('layouts.admin.sneat')
@section('content')
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col mt-5">
                    <h4 class="page-title">
                        Dashboard
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row d-flex justify-content-between flex-wrap">

            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3 flex-fill">
                <div class="card h-100 px-2 py-2">
                    <div class="card-body d-flex align-items-center px-2 py-2">
                        <div class="avatar avatar-sm me-3 d-flex align-items-center justify-content-center">
                            <span class="avatar-initial rounded bg-label-success d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bx bx-fingerprint fs-5"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-semibold">{{ $rekappresensi->jmlhadir != null ? $rekappresensi->jmlhadir : 0 }}</h6>
                            <small class="text-muted">Karyawan Hadir</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3 flex-fill">
                <div class="card h-100 px-2 py-2">
                    <div class="card-body d-flex align-items-center px-2 py-2">
                        <div class="avatar avatar-sm me-3 d-flex align-items-center justify-content-center">
                            <span class="avatar-initial rounded bg-label-info d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bx bx-file fs-5"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-semibold">{{ $rekapizin->jmlizin != null ? $rekapizin->jmlizin : 0 }}</h6>
                            <small class="text-muted">Karyawan Izin</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3 flex-fill">
                <div class="card h-100 px-2 py-2">
                    <div class="card-body d-flex align-items-center px-2 py-2">
                        <div class="avatar avatar-sm me-3 d-flex align-items-center justify-content-center">
                            <span class="avatar-initial rounded bg-label-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bx bx-first-aid fs-5"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-semibold">{{ $rekapizin->jmlsakit != null ? $rekapizin->jmlsakit : 0 }}</h6>
                            <small class="text-muted">Karyawan Sakit</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3 flex-fill">
                <div class="card h-100 px-2 py-2">
                    <div class="card-body d-flex align-items-center px-2 py-2">
                        <div class="avatar avatar-sm me-3 d-flex align-items-center justify-content-center">
                            <span class="avatar-initial rounded bg-label-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bx bx-timer fs-5"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-semibold">{{ $rekappresensi->jmlterlambat != null ? $rekappresensi->jmlterlambat : 0 }}</h6>
                            <small class="text-muted">Karyawan Telat</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3 flex-fill">
                <div class="card h-100 px-2 py-2">
                    <div class="card-body d-flex align-items-center px-2 py-2">
                        <div class="avatar avatar-sm me-3 d-flex align-items-center justify-content-center">
                            <span class="avatar-initial rounded bg-label-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class='bx  bx-x'  ></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-semibold">{{ $rekappresensi->jmlbelumabsen != null ? $rekappresensi->jmlbelumabsen : 0 }}</h6>
                            <small class="text-muted">Karyawan Belum Absen</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col mt-5">
                    <h4 class="page-title">
                        Monitoring Presensi
                    </h4>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row d-flex justify-content-between flex-wrap">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="tanggal" class="form-label">Tanggal Presensi:</label>
                                        <input type="date" value="{{ date("Y-m-d") }}" class="form-control" id="tanggal" name="tanggal" placeholder="Tanggal Presensi" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <table class="table table-bordered table-striped text-center">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>No.</th>
                                                <th>Id Karyawan</th>
                                                <th>Nama Karyawan</th>
                                                <th>Jabatan</th>
                                                <th>Jam Masuk</th>
                                                <th>Foto</th>
                                                <th>Jam Pulang</th>
                                                <th>Foto</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="loadpresensi"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(function() {
            function loadpresensi() {
                var tanggal = $("#tanggal").val();
                $.ajax({
                    type: 'POST',
                    url: '/getpresensi',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tanggal: tanggal
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadpresensi").html(respond);
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr.responseText);
                    }
                });
            }

            $("#tanggal").change(function() {
                loadpresensi();
            });

            // Load awal
            loadpresensi();
        });
    </script>
@endpush
