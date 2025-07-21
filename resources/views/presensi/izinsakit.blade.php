@extends('layouts.admin.sneat')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col mt-5">
                <h4 class="page-title">Data Izin / Sakit</h4>
            </div>
        </div>
    </div>
</div>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row d-flex justify-content-between flex-wrap">
        <form action="/presensi/izinsakit" method="GET">
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
            </div>

            <div class="row g-2 align-items-end mb-3">
                <div class="col-md-3">
                    <label for="id_karyawan" class="form-label">Id Karyawan</label>
                    <input type="text" name="id_karyawan" class="form-control" placeholder="Id Karyawan" value="{{ Request('id_karyawan') }}">
                </div>
                <div class="col-md-3">
                    <label for="nama" class="form-label">Nama Karyawan</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama Karyawan" value="{{ Request('nama') }}">
                </div>
                <div class="col-md-3">
                    <label for="status_approved" class="form-label">Status</label>
                    <select name="status_approved" class="form-select">
                        <option value="">Pilih Status</option>
                        <option value="0" {{ Request('status_approved') == '0' ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ Request('status_approved') == '1' ? 'selected' : '' }}>Disetujui</option>
                        <option value="2" {{ Request('status_approved') == '2' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <label class="form-label d-block">&nbsp;</label>
                    <button class="btn btn-primary"><i class='bx bx-search'></i> Cari</button>
                </div>
            </div>
        </form>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>ID Karyawan</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($izinsakit as $d)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ date('d-m-Y', strtotime($d->tgl_izin)) }}</td>
                            <td>{{ $d->id_karyawan }}</td>
                            <td>{{ $d->nama }}</td>
                            <td>{{ $d->jabatan }}</td>
                            <td>{{ $d->status == 'i' ? 'Izin' : 'Sakit' }}</td>
                            <td>
                                @if($d->status_approved == 1)
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($d->status_approved == 2)
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($d->file)
                                    <a href="{{ asset('storage/uploads/perizinan/' . $d->file) }}" target="_blank" class="btn btn-info btn-sm">Lihat Bukti</a>
                                @elseif($d->keterangan)
                                    <button type="button" class="btn btn-secondary btn-sm lihat-keterangan" data-keterangan="{{ $d->keterangan }}">
                                        Lihat Keterangan
                                    </button>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($d->status_approved == 0)
                                    <a href="#" class="btn btn-sm btn-primary" id="approved" id_izinsakit="{{ $d->id }}">
                                        <i class='bx bx-edit'></i>
                                    </a>
                                @else
                                    <a href="/presensi/{{ $d->id }}/batalkanizinsakit" class="btn btn-sm btn-danger">
                                        <i class='bx bx-trash'></i>
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

<div class="modal fade" id="modalKeterangan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Keterangan Izin/Sakit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="isiKeterangan" class="alert alert-info"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-izinsakit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="/presensi/approvedizinsakit" method="POST">
                @csrf
                <input type="hidden" id="id_izinsakit_form" name="id_izinsakit_form">
                <div class="modal-header">
                    <h5 class="modal-title">Setujui / Tolak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <select name="status_approved" class="form-select">
                        <option value="1">Setujui</option>
                        <option value="2">Tolak</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function () {
        $(document).on("click", "#approved", function (e) {
            e.preventDefault();
            let id = $(this).attr("id_izinsakit");
            $("#id_izinsakit_form").val(id);
            $("#modal-izinsakit").modal("show");
        });

        $(document).on("click", ".lihat-keterangan", function () {
            let ket = $(this).data("keterangan");
            $("#isiKeterangan").text(ket);
            $("#modalKeterangan").modal("show");
        });
    });
</script>
@endpush
