@extends('layouts.presensi')
@section('header')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <div class="appHeader bg-danger text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Rekap Presensi</div>
        <div class="right"></div>
    </div>
@endsection

{{-- @section('content')
    <div class="row" style="margin-top: 70px">
        <div class="col">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <select name="bulan" id="bulan" class="form-control">
                            <option value="">Bulan</option>
                            @for ($i=1; $i<=12; $i++)
                                <option value="{{ $i }}" {{ date("m") == $i ? 'selected' : '' }}>{{ $namabulan[$i] }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <select name="tahun" id="tahun" class="form-control">
                            <option value="">Tahun</option>
                            @php
                                $tahunmulai = 2025;
                                $tahunskrg = date("Y");
                            @endphp
                            @for ($tahun=$tahunmulai; $tahun<=$tahunskrg; $tahun++)
                                <option value="{{ $tahun }}" {{ date("Y") == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <button class="btn btn-danger btn-block" id="getdata">
                            <ion-icon name="search-outline"></ion-icon>
                            Cari
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col" id="showrekap"></div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            $("#getdata").click(function(e) {
                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();
                $.ajax({
                    type: 'POST',
                    url: '/getrekap',
                    data: {
                        _token: "{{ csrf_token() }}",
                        bulan: bulan,
                        tahun: tahun
                    },
                    cache:false,
                    success: function(respond) {
                        $("#showrekap").html(respond);
                    }
                });
            });
        });
    </script>
@endpush --}}

@section('content')
    <div class="container" style="margin-top: 70px;">
        <form id="filterForm">
            @csrf
            <div class="d-flex align-items-end gap-3">
                <div class="form-group">
                    <label for="start_date" class="form-label">Start Date:</label>
                    <input type="date" class="form-control" name="start_date" id="start_date" required>
                </div>
                <div class="form-group">
                    <label for="end_date" class="form-label">End Date:</label>
                    <input type="date" class="form-control" name="end_date" id="end_date" required>
                </div>
            </div>
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <button type="submit" class="btn btn-danger btn-block">
                        <ion-icon name="search-outline"></ion-icon>
                        Filter
                    </button>
                </div>
            </div>

            {{-- <div class="form-group">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" class="btn btn-danger d-flex align-items-center justify-content-center" style="width: 100px;">
                    <ion-icon name="search-outline" class="me-2"></ion-icon>
                    <span>Filter</span>
                </button>
            </div> --}}

            {{-- <div class="border rounded p-3 mt-3" style="background-color: #ffffff;">
                <div class="row g-2">
                    <div class="col">
                        <button type="submit" name="status" value="Hadir" class="btn btn-success btn-sm w-100">Hadir</button>
                    </div>
                    <div class="col">
                        <button type="submit" name="status" value="Sakit" class="btn btn-danger btn-sm w-100">Sakit</button>
                    </div>
                    <div class="col">
                        <button type="submit" name="status" value="Izin" class="btn btn-primary btn-sm w-100">Izin</button>
                    </div>
                    <div class="col">
                        <button type="submit" name="status" value="Telat" class="btn btn-warning btn-sm w-100">Telat</button>
                    </div>
                </div>
            </div> --}}
        </form>

        <div id="showrekap" class="mt-4"></div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function () {
            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                const formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: '/getrekap',
                    data: formData,
                    success: function (response) {
                        $('#showrekap').html(response);
                    },
                    error: function () {
                        alert('Gagal memuat data.');
                    }
                });
            });
        });
    </script>
@endpush
