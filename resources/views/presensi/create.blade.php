@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-danger text-light">
        <div class="left">
            <a href="{{ url()->previous() }}" class="headerButton">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">X-Presensi</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->

    <style>
        .webcam-capture,
        .webcam-capture video {
            display: inline-block;
            width: 100% !important;
            margin: auto;
            height: auto !important;
            border-radius: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="row mt-4">
        <div class="col">
            <div class="webcam-capture"></div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
            @if ($cek > 0)
                <button id="takeabsen" class="btn btn-danger btn-block">
                    <ion-icon name="camera-outline"></ion-icon> Absen Pulang
                </button>
            @else
                <button id="takeabsen" class="btn btn-primary btn-block">
                    <ion-icon name="camera-outline"></ion-icon> Absen Masuk
                </button>
            @endif
        </div>
    </div>
@endsection

@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/webcamjs/webcam.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        Webcam.set({
            height: 480,
            width: 640,
            image_format: 'jpeg',
            jpeg_quality: 80
        });

        Webcam.attach('.webcam-capture');

        let image = '';

        $("#takeabsen").click(function () {
            Webcam.snap(function (uri) {
                image = uri;

                $.ajax({
                    type: 'POST',
                    url: '/presensi/store',
                    data: {
                        _token: "{{ csrf_token() }}",
                        image: image
                    },
                    success: function (respond) {
                        let status = respond.split("|");
                        if (status[0] === "success") {
                            Swal.fire('Berhasil!', status[1], 'success');
                            setTimeout(() => location.href = '/dashboard', 1000);
                        } else {
                            Swal.fire('Gagal!', status[1], 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Gagal!', 'Tidak dapat absen, periksa jaringan.', 'error');
                    }
                });
            });
        });
    </script>
@endpush
