@extends('layouts.admin.sneat')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col mt-5">
                <h4 class="page-title">Data Karyawan</h4>
            </div>
        </div>
    </div>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row d-flex justify-content-between flex-wrap">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                @if (Session::get('success'))
                                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                                @endif
                                @if (Session::get('warning'))
                                    <div class="alert alert-warning">{{ Session::get('warning') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <a href="#" class="btn btn-primary" id="btnTambahkaryawan">
                                    <i class='bx bx-plus'></i> Tambah Data
                                </a>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <form action="/karyawan" method="GET">
                                    @csrf
                                    <div class="row">
                                        <div class="col-4">
                                            <input type="text" name="nama" id="cari_nama" class="form-control" placeholder="Nama Karyawan" value="{{ request('nama') }}">
                                        </div>
                                        <div class="col-4">
                                            <button type="submit" class="btn btn-primary w-30">
                                                <i class='bx bx-search'></i> Cari
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <table class="table table-bordered bg-white text-center">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Id Karyawan</th>
                                            <th>Nama Karyawan</th>
                                            <th>Jabatan</th>
                                            <th>No. Hp</th>
                                            <th>Aksi</th>
                                            <th>Status Approved</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $sorted = $karyawan->sortByDesc(function ($item) {
                                                return DB::table('pengajuan_update_profil')
                                                    ->where('id_karyawan', $item->id_karyawan)
                                                    ->where('status', 'pending')
                                                    ->exists() ? 1 : 0;
                                            });
                                            $i = 1 + ($karyawan->currentPage() - 1) * $karyawan->perPage();
                                        @endphp
                                        @foreach ($sorted as $d)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $d->id_karyawan }}</td>
                                                <td>{{ $d->nama }}</td>
                                                <td>{{ $d->jabatan }}</td>
                                                <td>{{ $d->no_hp }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button href="#" class="btn btn-warning btn-sm me-2 edit" id_karyawan="{{ $d->id_karyawan }}">
                                                            <i class='bx bx-edit'></i>
                                                        </button>
                                                        <form action="/karyawan/{{ $d->id_karyawan }}/delete" method="POST" class="form-delete">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-danger btn-sm delete-confirm">
                                                                <i class='bx bx-trash'></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $pengajuan = DB::table('pengajuan_update_profil')
                                                            ->where('id_karyawan', $d->id_karyawan)
                                                            ->where('status', 'pending')
                                                            ->orderBy('created_at', 'desc')
                                                            ->first();
                                                    @endphp
                                                    @if ($pengajuan && $pengajuan->status == 'pending')
                                                        <div class="dropdown">
                                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                Persetujuan
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <form action="/pengajuanprofil/{{ $pengajuan->id }}/verifikasi" method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="aksi" value="setuju">
                                                                        <button class="dropdown-item text-success" type="submit">Setujui</button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form action="/pengajuanprofil/{{ $pengajuan->id }}/verifikasi" method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="aksi" value="tolak">
                                                                        <button class="dropdown-item text-danger" type="submit">Tolak</button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @elseif ($pengajuan && $pengajuan->status == 'disetujui')
                                                        <span class="badge bg-success">Telah Disetujui</span>
                                                    @elseif ($pengajuan && $pengajuan->status == 'ditolak')
                                                        <span class="badge bg-danger">Ditolak</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="mt-3">
                                    {{ $karyawan->links('vendor.pagination.bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-inputkaryawan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCenterTitle">Tambah Data Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/karyawan/store" method="POST" id="frmkaryawan" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="id_karyawan" class="form-label">Id Karyawan</label>
                            <input type="text" id="id_karyawan" name="id_karyawan" class="form-control" placeholder="Id Karyawan"/>
                        </div>
                        <div class="col mb-0">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" id="nama" name="nama" class="form-control" placeholder="nama"/>
                        </div>
                    </div>
                    <div class="row g-2 mt-3">
                        <div class="col mb-0">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" id="jabatan" name="jabatan" class="form-control" placeholder="Jabatan"/>
                        </div>
                        <div class="col mb-0">
                            <label for="no_hp" class="form-label">No Hp</label>
                            <input type="text" id="no_hp" name="no_hp" class="form-control" placeholder="No HP"/>
                        </div>
                    </div>
                    <div class="modal-footer mt-3">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-editkaryawan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCenterTitle">Edit Data Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="loadeditform">

            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        $("#btnTambahkaryawan").click(function() {
            $("#modal-inputkaryawan").modal("show");
        });

        $(".edit").click(function() {
            var id_karyawan = $(this).attr('id_karyawan');
            $.ajax({
                type: 'POST',
                url: '/karyawan/edit',
                cache: false,
                data: {
                    _token: "{{ csrf_token() }}",
                    id_karyawan: id_karyawan
                },
                success: function(respond) {
                    $("#loadeditform").html(respond);
                }
            });
            $("#modal-editkaryawan").modal("show");
        });

        $(".delete-confirm").click(function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            Swal.fire({
                title: "Apakah Anda Yakin Data Ini Mau di Hapus?",
                text: "Jika Iya Maka akan di Hapus Permanen",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Iya, Hapus Saja!"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
