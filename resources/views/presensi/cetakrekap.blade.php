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
        size: A4 landscape;
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
        font-size: 8.5px;
    }

    .foto {
        width: 40px;
        height: 30px;
    }

  </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4 landscape">
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

    <table class="tablepresensi">
        <tr>
            <th rowspan="2">Id Karyawan</th>
            <th rowspan="2">Nama Karyawan</th>
            <th colspan="31">Tanggal</th>
            <th rowspan="2">TH</th>
            <th rowspan="2">TT</th>
        </tr>
        <tr>
            @php
                for($i = 1; $i <= 31; $i++) {
                    echo "<th>$i</th>";
                }
            @endphp
        </tr>
        <tr>
            @foreach ($rekap as $d)
                <tr>
                    <td>{{ $d->id_karyawan }}</td>
                    <td>{{ $d->nama }}</td>
                    @php
                        $totalhadir = 0;
                        $totalterlambat = 0;
                    @endphp
                    @for ($i = 1; $i <= 31; $i++)
                        @php
                            $tgl = "tgl_" . $i;
                            if (empty($d->$tgl)) {
                                $hadir = ['', ''];
                            } else {
                                $hadir = explode('-', $d->$tgl);
                                $totalhadir += 1;
                                if ($hadir[0] > "09:00:00") {
                                    $totalterlambat += 1;
                                }
                            }
                        @endphp
                        <td>
                            @php
                                $jamMasuk = $hadir[0] ?? '';
                                $jamPulang = $hadir[1] ?? '';
                                $colorMasuk = 'black';
                                $colorPulang = 'black';

                                if ($jamMasuk != '') {
                                    if ($jamMasuk < '08:00:00') {
                                        $colorMasuk = 'orange';
                                    } elseif ($jamMasuk > '09:00:00') {
                                        $colorMasuk = 'red';
                                    }
                                }

                                if ($jamPulang != '') {
                                    if ($jamPulang < '17:00:00' || $jamPulang > '23:59:59') {
                                        $colorPulang = 'orange';
                                    }
                                }
                            @endphp

                            <span style="color: {{ $colorMasuk }}">
                                {{ $jamMasuk }}
                            </span><br>

                            <span style="color: {{ $colorPulang }}">
                                {{ $jamPulang }}
                            </span>
                        </td>
                    @endfor
                    <td>{{ $totalhadir }}</td>
                    <td>{{ $totalterlambat }}</td>
                </tr>
            @endforeach
        </tr>
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
