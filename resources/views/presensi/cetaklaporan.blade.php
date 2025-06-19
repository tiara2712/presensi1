<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>A4</title>

  <!-- Normalize or reset CSS with your favorite library -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <!-- Load paper.css for happy printing -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

  <!-- Set page size here: A5, A4 or A3 -->
  <!-- Set also "landscape" if you need -->
  <style>
    @page {
        size: A4
    }

    .tabledatakaryawan {
        margin-top: 40px;
    }

    .tabledatakaryawan td {
        padding: 5px;
    }

    .tablepresensi {
        width: 100%;
        border-collapse: collapse;
        margin: 20px auto;
        font-family: Arial, sans-serif;
        font-size: 13px;
    }

    .tablepresensi th,
    .tablepresensi td {
        border: 1px solid #000;
        padding: 8px;
        text-align: center;
    }

    .foto {
        width: 40px;
        height: 30px;
    }

  </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4">
    {{-- @php
        function selisih($jam_masuk, $jam_keluar)
            {
                list($h, $m, $s) = explode(":", $jam_masuk);
                $dtAwal = mktime($h, $m, $s, "1", "1", "1");
                list($h, $m, $s) = explode(":", $jam_keluar);
                $dtAkhir = mktime($h, $m, $s, "1", "1", "1");
                $dtSelisih = $dtAkhir - $dtAwal;
                $totalmenit = $dtSelisih / 60;
                $jam = explode(".", $totalmenit / 60);
                $sisamenit = ($totalmenit / 60) - $jam[0];
                $sisamenit2 = $sisamenit * 60;
                $jml_jam = $jam[0];
                return $jml_jam . ":" . round($sisamenit2);
            }
    @endphp --}}
  <section class="sheet padding-10mm">

    <table style="width: 100%;">
        <tr>
            <td style="width: 200px;">
                <img src="{{ public_path('assets/img/login/logo.png') }}" width="180" height="75" alt="">
            </td>
            <td style="text-align: left; padding-right: 10px;">
                <h3 style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px;">
                    LAPORAN PRESENSI KARYAWAN<br>
                    PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}<br>
                    NINJA XPRESS GBN BANYUWANGI<br>
                </h3>
            </td>
        </tr>
    </table>
    <table class="tabledatakaryawan" style="font-family: Arial, sans-serif; font-size: 14px;">
        <tr>
            <td rowspan="5" style="vertical-align: top; padding-right: 15px;">
                @php
                    $path = public_path('storage/uploads/karyawan/' . $karyawan->foto);
                @endphp
                <img src="{{ $path }}" alt="Foto Karyawan" width="100px" height="120px" style="margin-top: 5px;">
            </td>
            <td><strong>Id Karyawan</strong></td>
            <td><strong>:</strong></td>
            <td><strong>{{ $karyawan->id_karyawan }}</strong></td>
        </tr>
        <tr>
            <td><strong>Nama Karyawan</strong></td>
            <td><strong>:</strong></td>
            <td><strong>{{ $karyawan->nama }}</strong></td>
        </tr>
        <tr>
            <td><strong>Jabatan</strong></td>
            <td><strong>:</strong></td>
            <td><strong>{{ $karyawan->jabatan }}</strong></td>
        </tr>
        <tr>
            <td><strong>No. HP</strong></td>
            <td><strong>:</strong></td>
            <td><strong>{{ $karyawan->no_hp }}</strong></td>
        </tr>
    </table>
    <table class="tablepresensi">
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Jam Masuk</th>
            <th>Foto</th>
            <th>Jam Pulang</th>
            <th>Foto</th>
            <th>Keterangan</th>
        </tr>
        @foreach ($presensi as $d)
            @php
                $path_in = public_path('storage/uploads/absensi/' . $d->foto_in);
                $path_out = public_path('storage/uploads/absensi/' . $d->foto_out);
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ date("d-m-Y", strtotime($d->tgl_presensi)) }}</td>

                @php
                    $colorMasuk = 'black';
                    $colorPulang = 'black';
                    $keterangan = 'Tepat Waktu';

                    if ($d->jam_in) {
                        if ($d->jam_in < '08:00:00') {
                            $colorMasuk = 'orange';
                        } elseif ($d->jam_in > '09:00:00') {
                            $colorMasuk = 'red';
                            $keterangan = 'Terlambat';
                        }
                    }

                    if ($d->jam_out) {
                        if ($d->jam_out < '17:00:00' || $d->jam_out > '23:59:59') {
                            $colorPulang = 'orange';
                        }
                    } else {
                        $keterangan = 'Belum Absen Pulang';
                    }
                @endphp

                <td style="color: {{ $colorMasuk }}">{{ $d->jam_in }}</td>
                <td style="text-align: center;"><img src="{{ $path_in }}" alt="" class="foto"></td>

                <td style="color: {{ $colorPulang }}">
                    {{ $d->jam_out != null ? $d->jam_out : 'Belum Absen' }}
                </td>
                <td>
                    @if ($d->jam_out != null)
                        <img src="{{ $path_out }}" alt="" class="foto">
                    @else
                        <span style="font-size: 12px;">(Tidak ada Foto)</span>
                    @endif
                </td>

                <td>{{ $keterangan }}</td>
            </tr>
        @endforeach
    </table>
    <table style="width: 100%; font-family: Arial, sans-serif; font-size: 14px; margin-top: 80px;">
        <tr>
            <td style="width: 100%;">
                <div style="width: 300px; float: right; text-align: center;">
                    Banyuwangi, {{ date('d-m-Y') }}<br><br><br><br><br>
                    <span style="text-decoration: underline;">Supervisor</span>
                </div>
            </td>
        </tr>
    </table>

  </section>

</body>

</html>
