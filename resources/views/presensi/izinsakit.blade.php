@extends('layouts.admin.sneat')
@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col mt-5">
                    <h4 class="page-title">
                        Data Izin / Sakit
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row d-flex justify-content-between flex-wrap">
            <div class="row">
                <div class="col-12">
                    <form action="/presensi/izinsakit" method="GET">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="col mb-0">
                                    <label for="start_date" class="form-label">Start Date:</label>
                                    <input type="date" value="{{ Request('start_date') }}" id="start_date" name="start_date" class="form-control" placeholder="Start Date"/>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="col mb-0">
                                    <label for="end_date" class="form-label">End Date:</label>
                                    <input type="date" value="{{ Request('end_date') }}" id="end_date" name="end_date" class="form-control" placeholder="End Date"/>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2 align-items-end">
                            <div class="col-md-3">
                                <label for="id_karyawan" class="form-label">Id Karyawan:</label>
                                <input type="text" value="{{ Request('id_karyawan') }}" id="id_karyawan" name="id_karyawan" class="form-control" placeholder="Id Karyawan"/>
                            </div>
                            <div class="col-md-3">
                                <label for="nama" class="form-label">Nama Karyawan:</label>
                                <input type="text" value="{{ Request('nama') }}" id="nama" name="nama" class="form-control" placeholder="Nama Karyawan"/>
                            </div>
                            <div class="col-md-3">
                                <label for="status_approved" class="form-label">Status:</label>
                                <select name="status_approved" id="status_approved" value="{{ Request('status_approved') }}" class="form-select">
                                    <option value="">Pilih Status</option>
                                    <option value="0 {{ Request('status_approved') == 0 ? 'selected' : "" }}">Pending</option>
                                    <option value="1 {{ Request('status_approved') == 1 ? 'selected' : "" }}">Disetujui</option>
                                    <option value="2 {{ Request('status_approved') == 2 ? 'selected' : "" }}">Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary">
                                    <i class='bx  bx-search'></i>
                                    Cari
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card bg-white shadow-sm">
                        <div class="card-body">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Tanggal</th>
                                        <th>Id Karyawan</th>
                                        <th>Nama Karyawan</th>
                                        <th>Jabatan</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($izinsakit as $d)
                                        <tr class="bg-white">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ date('d-m-Y' , strtotime($d->tgl_izin)) }}</td>
                                            <td>{{ $d->id_karyawan }}</td>
                                            <td>{{ $d->nama }}</td>
                                            <td>{{ $d->jabatan }}</td>
                                            <td>{{ $d->status == "i" ? "Izin" : "Sakit" }}</td>
                                            <td>
                                                @if ($d->status_approved == 1)
                                                    <span class="badge bg-success">Disetujui</span>
                                                @elseif ($d->status_approved == 2)
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($d->status_approved == 0)
                                                    <a href="#" class="btn btn-sm btn-primary" id="approved" id_izinsakit="{{ $d->id }}">
                                                        <i class='bx  bx-edit'></i>
                                                    </a>
                                                @else
                                                    <a href="/presensi/{{ $d->id }}/batalkanizinsakit" class="btn btn-sm btn-danger">
                                                        <i class='bx  bx-trash'></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                {{ $izinsakit->links('vendor.pagination.bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-izinsakit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Izin / Sakit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="loadeditform">
                    <form action="/presensi/approvedizinsakit" method="POST">
                        @csrf
                        <input type="hidden" id="id_izinsakit_form" name="id_izinsakit_form">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="status_approved" id="status_approved" class="form-select">
                                        <option value="1">Disetujui</option>
                                        <option value="2">Ditolak</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 text-end">
                                <div class="form-group">
                                    <button class="btn btn-primary" type="submit">
                                        submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(function() {
            $("#approved").click(function(e) {
                e.preventDefault();
                var id_izinsakit = $(this).attr("id_izinsakit");
                $("#id_izinsakit_form").val(id_izinsakit);
                $("#modal-izinsakit").modal("show");
            });
        });
    </script>
@endpush
