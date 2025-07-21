@extends('layouts.presensi')
@section('header')
    <div class="appHeader bg-danger text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Izin / Sakit</div>
        <div class="right"></div>
    </div>
@endsection
@section('content')
    <div class="row" style="margin-top: 70px">
        <div class="col">
            <form action="/upload" method="POST" id="formizin" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <input type="date" id="tgl_izin" name="tgl_izin" class="form-control" placeholder="Tanggal">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <select name="status" id="status" class="form-control">
                                <option value="">Pilih Izin / Sakit:</option>
                                <option value="i">Izin</option>
                                <option value="s">Sakit</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" id="form-keterangan" style="display: none;">
                    <div class="col">
                        <div class="form-group">
                            <textarea name="keterangan" id="keterangan" cols="30" rows="5" class="form-control" placeholder="Keterangan"></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="form-upload" style="display: none;">
                    <div class="custom-file-upload">
                        <input type="file" name="file" id="fileuploadInput" accept=".pdf">
                        <label for="fileuploadInput">
                            <span>
                                <strong>
                                    <ion-icon name="cloud-upload-outline"></ion-icon>
                                    <i>Upload Surat Sakit</i>
                                </strong>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-danger w-100">Kirim</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(document).ready(function () {

            $("#tgl_izin").change(function (e) {
                var tgl_izin = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: '/presensi/cekperizinan',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tgl_izin: tgl_izin
                    },
                    cache: false,
                    success: function (respond) {
                        if (respond == 1) {
                            Swal.fire({
                                title: 'Oops !',
                                text: 'Anda sudah mengajukan izin/sakit di tanggal tersebut!',
                                icon: 'warning'
                            }).then((result) => {
                                $("#tgl_izin").val("");
                            });
                        }
                    }
                });
            });

            $("#status").change(function () {
                var selected = $(this).val();
                if (selected === "i") {
                    $("#form-keterangan").show();
                    $("#form-upload").hide();
                    $("#fileuploadInput").val("");
                } else if (selected === "s") {
                    $("#form-keterangan").hide();
                    $("#form-upload").show();
                    $("#keterangan").val("");
                } else {
                    $("#form-keterangan").hide();
                    $("#form-upload").hide();
                    $("#keterangan").val("");
                    $("#fileuploadInput").val("");
                }
            });

            $("#formizin").submit(function () {
                var tgl_izin = $("#tgl_izin").val();
                var status = $("#status").val();

                if (tgl_izin == "") {
                    Swal.fire({ title: 'Oops !', text: 'Tanggal harus diisi', icon: 'warning' });
                    return false;
                } else if (status == "") {
                    Swal.fire({ title: 'Oops !', text: 'Status harus diisi', icon: 'warning' });
                    return false;
                } else if (status == "i" && $("#keterangan").val() == "") {
                    Swal.fire({ title: 'Oops !', text: 'Keterangan harus diisi untuk izin', icon: 'warning' });
                    return false;
                } else if (status == "s" && $("#fileuploadInput").get(0).files.length === 0) {
                    Swal.fire({ title: 'Oops !', text: 'Upload PDF surat keterangan sakit wajib diisi', icon: 'warning' });
                    return false;
                }
            });
        });
    </script>
@endpush
