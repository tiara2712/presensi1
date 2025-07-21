@extends('layouts.admin.sneat')

@section('content')
<div class="container-xl mt-5">
    <h4 class="page-title">Laporan Rekap Karyawan</h4>

    <form action="/presensi/laporanrekap" method="GET" class="row g-3 align-items-end my-3">
        @csrf
        <div class="row g-2 align-items-end mb-2">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ Request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ Request('end_date') }}">
            </div>
            <div class="col-md-3">
                <label for="nama" class="form-label">Nama Karyawan</label>
                <input type="text" name="nama" class="form-control" value="{{ request('nama') }}" placeholder="Nama Karyawan">
            </div>
        {{-- </div> --}}
            {{-- <div class="col-md-2">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select">
                    <option value="">Pilih Bulan</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ $namabulan[$i] }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select">
                    <option value="">Pilih Tahun</option>
                    @php
                        $tahunmulai = 2024;
                        $tahunsekarang = date('Y');
                    @endphp
                    @for ($i = $tahunmulai; $i <= $tahunsekarang; $i++)
                        <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div> --}}
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-40">
                    <i class='bx bx-search'></i> Cari
                </button>
            </div>
        </div>
        <div class="col-12 d-flex justify-content-end mt-2">
            <a href="{{ route('presensi.laporanrekap.cetak', request()->query()) }}" target="_blank" class="btn btn-danger">
                <i class='bx bx-printer'></i> Cetak
            </a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No.</th>
                        <th>ID Karyawan</th>
                        <th>Nama Karyawan</th>
                        <th>Jumlah Hadir</th>
                        <th>Jumlah Terlambat</th>
                        <th>Jumlah Sakit</th>
                        <th>Jumlah Izin</th>
                        <th>Jumlah Belum Absen Presensi</th>
                        <th>Jumlah Belum Absen Pulang</th>
                        <th>Jumlah Lembur</th>
                        {{-- <th>IP Karyawan</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekap as $i => $r)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $r->id_karyawan }}</td>
                            <td>{{ $r->nama }}</td>
                            <td>{{ $r->jumlah_hadir }}</td>
                            <td>{{ $r->jumlah_terlambat }}</td>
                            <td>{{ $r->jumlah_sakit }}</td>
                            <td>{{ $r->jumlah_izin }}</td>
                            <td>{{ $r->jumlah_belum_absen }}</td>
                            <td>{{ $r->jumlah_belum_absen_pulang }}</td>
                            <td>{{ $r->jumlah_jam_tambahan }} jam</td>
                            {{-- <td>{{ $r->ip_address }}</td> --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
