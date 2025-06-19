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
                                <option value="">Izin / Sakit</option>
                                <option value="i">Izin</option>
                                <option value="s">Sakit</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-file-upload" id="fileUpload1">
                        <input type="file" name="file" id="fileuploadInput" accept=".png, .jpg, .jpeg, .pdf">
                        <label for="fileuploadInput">
                            <span>
                                <strong>
                                    <ion-icon name="cloud-upload-outline" role="img" class="md hydrated" aria-label="cloud upload outline"></ion-icon>
                                    <i>Tap to Upload</i>
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
        var currYear = (new Date()).getFullYear();
        $(document).ready(function() {
            $(".datepicker").datepicker({
                format: "yyyy-mm-dd"
            });

            $("#tgl_izin").change(function(e) {
                var tgl_izin = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: '/presensi/cekperizinan',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tgl_izin: tgl_izin
                    },
                    cache: false,
                    success: function(respond) {
                        if (respond==1) {
                            Swal.fire({
                                title: 'Oops !',
                                text: 'Anda Sudah Melakukan Input Pengajuan Izin / Sakit Pada Tanggal Tersebut !',
                                icon: 'warning'
                            }).then((result) => {
                                $("#tgl_izin").val("");
                            });
                        }
                    }
                });
            });

            $("#formizin").submit(function(){
                var tgl_izin = $("#tgl_izin").val();
                var status = $("#status").val();
                if (tgl_izin == "") {
                    Swal.fire({
                        title: 'Oops !',
                        text: 'Tanggal harus di isi',
                        icon: 'warning'
                    });
                    return false;
                } else if (status == "") {
                    Swal.fire({
                        title: 'Oops !',
                        text: 'Status harus di isi',
                        icon: 'warning'
                    });
                    return false;
                }
            });
        });
    </script>
@endpush
