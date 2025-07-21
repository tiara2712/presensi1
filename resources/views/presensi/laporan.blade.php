@extends('layouts.admin.sneat')
@section('content')
<div class="container-xxl mt-5">
    <h4 class="mb-4">Laporan Presensi Karyawan</h4>

    <form method="GET" action="{{ route('presensi.laporan') }}">
        <div class="row g-2 align-items-end mb-2">
            <div class="col-md-3">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-4">
                <label>Nama Karyawan</label>
                <select name="id_karyawan" class="form-select">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($karyawan as $k)
                        <option value="{{ $k->id_karyawan }}" {{ request('id_karyawan') == $k->id_karyawan ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-40">
                    <i class='bx bx-search'></i> Cari
                </button>
            </div>
        </div>
    </form>

    @if (count($rekap))
        <form method="POST" action="{{ route('presensi.cetaklaporan') }}" target="_blank" class="mt-4">
            @csrf
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            <input type="hidden" name="id_karyawan" value="{{ request('id_karyawan') }}">
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success">
                    <i class="bx bx-printer"></i> Cetak PDF
                </button>
            </div>
        </form>
    @endif

    <div class="card mt-4">
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Karyawan</th>
                        <th>Nama</th>
                        <th>Hadir</th>
                        <th>Terlambat</th>
                        {{-- <th>Sakit</th>
                        <th>Izin</th> --}}
                        <th>Belum Absen Masuk</th>
                        <th>Belum Absen Pulang</th>
                        <th>Lembur (Jam)</th>
                        <th>IP Karyawan</th>
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
                            {{-- <td>{{ $r->jumlah_sakit }}</td>
                            <td>{{ $r->jumlah_izin }}</td> --}}
                            <td>{{ $r->jumlah_belum_absen }}</td>
                            <td>{{ $r->jumlah_belum_absen_pulang }}</td>
                            <td>{{ $r->jumlah_jam_tambahan }}</td>
                            <td>{{ $r->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
