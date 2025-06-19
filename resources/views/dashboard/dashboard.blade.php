@extends('layouts.presensi')
@section('content')
    <div class="section" id="user-section">
        <div id="user-detail">
            <div class="avatar">
                @if (!empty(Auth::guard('karyawan')->user()->foto))
                    @php
                        $path = Storage::url('uploads/karyawan/' . Auth::guard('karyawan')->user()->foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="avatar" style="width: 75px; height: 75px; border-radius: 50%; object-fit: cover;">
                @else
                    <img src="assets/img/sample/avatar/avatar1.jpg" alt="avatar" class="imaged w64 rounded">
                @endif
            </div>
            <div id="user-info">
                <h2 id="user-name">{{ $karyawan->nama }}</h2>
                <span id="user-role">{{ $karyawan->jabatan }}</span>
            </div>
            <div style="margin-right: 20px;">
                <a href="{{ url('/proseslogout') }}">
                    <div class="menu-icon">
                        <ion-icon name="log-out-outline" style="font-size: 45px; color: white;"></ion-icon>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="section" id="menu-section">
        <div class="card">
            <div class="card-body text-center">
                <div class="list-menu">
                    <div class="item-menu text-center">
                        <span class="badge bg-danger" style="position: absolute; top:3px;">{{ $rekappresensi->jmlhadir }}</span>
                        <div class="menu-icon">
                            <a class="green" style="font-size: 40px;">
                                <ion-icon name="accessibility"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Hadir</span>
                        </div>
                    </div>
                    <div class="item-menu text-center">
                        <span class="badge bg-danger" style="position: absolute; top:3px;">{{ $rekapizin->jmlizin }}</span>
                        <div class="menu-icon">
                            <a href="" class="primary" style="font-size: 40px;">
                                <ion-icon name="reader"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Izin</span>
                        </div>
                    </div>
                    <div class="item-menu text-center">
                        <span class="badge bg-danger" style="position: absolute; top:3px;">{{ $rekapizin->jmlsakit }}</span>
                        <div class="menu-icon">
                            <a href="" class="danger" style="font-size: 40px;">
                                <ion-icon name="fitness"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Sakit</span>
                        </div>
                    </div>
                    <div class="item-menu text-center">
                        <span class="badge bg-danger" style="position: absolute; top:3px;">{{ $rekappresensi->jmlterlambat }}</span>
                        <div class="menu-icon">
                            <a href="" class="orange" style="font-size: 40px;">
                                <ion-icon name="timer"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            Telat
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="section mt-2" id="presence-section">
        <div class="todaypresence">
            <div class="row">
                <div class="col-6">
                    <div class="card gradasigreen">
                        <div class="card-body">
                            <div class="presencecontent">
                                <div class="iconpresence">
                                    <ion-icon name="download-outline"></ion-icon>
                                </div>
                                <div class="presencedetail">
                                    <h4 class="presencetitle">Masuk</h4>
                                    <span>{{ $presensihariini != null ? $presensihariini->jam_in : 'Belum Absen' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card gradasired">
                        <div class="card-body">
                            <div class="presencecontent">
                                <div class="iconpresence">
                                    <ion-icon name="share-outline"></ion-icon>
                                </div>
                                <div class="presencedetail">
                                    <h4 class="presencetitle">Pulang</h4>
                                    <span>{{ $presensihariini != null && $presensihariini->jam_out != null ? $presensihariini->jam_out : 'Belum Absen' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="presencetab mt-2">
            <div class="tab-pane fade show active" id="pilled" role="tabpanel">
                <ul class="nav nav-tabs style1" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                            Rekap Presensi Bulan {{ $namabulan[$bulanini] }} Tahun {{ $tahunini }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content mt-2" style="margin-bottom:100px;">
                <div class="tab-pane fade show active" id="home" role="tabpanel">
                    <ul class="listview image-listview">
                        <table class="table table-bordered table-striped text-center">
                            <thead class="bg-white text-dark">
                                <tr>
                                    <th>Tanggal Presensi</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($historibulanini as $d)
                                @php
                                    $path = Storage::url('uploads/absensi/'.$d->foto_in);
                                @endphp
                                <tr>
                                    <td>{{ date("d-m-Y", strtotime($d->tgl_presensi)) }}</td>
                                    @php
                                        $jam_masuk = strtotime($d->jam_in);
                                        $warna_masuk = '';
                                        if ($jam_masuk < strtotime('08:00:00')) {
                                            $warna_masuk = 'warning';
                                        } elseif ($jam_masuk >= strtotime('08:00:00') && $jam_masuk <= strtotime('09:00:00')) {
                                            $warna_masuk = 'success';
                                        } else {
                                            $warna_masuk = 'danger';
                                        }

                                        $jam_pulang = $d->jam_out ? strtotime($d->jam_out) : null;
                                        $warna_pulang = 'secondary';
                                        if ($jam_pulang) {
                                            if ($jam_pulang < strtotime('17:00:00') || $jam_pulang > strtotime('23:59:59')) {
                                                $warna_pulang = 'warning';
                                            } else {
                                                $warna_pulang = 'success';
                                            }
                                        }
                                    @endphp

                                    <td><span class="badge badge-{{ $warna_masuk }}">{{ $d->jam_in }}</span></td>
                                    <td>
                                        @if ($d->jam_out != null)
                                            <span class="badge badge-{{ $warna_pulang }}">{{ $d->jam_out }}</span>
                                        @else
                                            <span class="badge badge-danger">Belum Absen</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
