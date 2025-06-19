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
        size: A4;
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

    <section class="sheet padding-10mm">

        <!-- Header -->
        <table style="width: 100%;">
            <tr>
                <td style="width: 200px;">
                    <img src="{{ public_path('assets/img/login/logo.png') }}" width="180" height="75" alt="">
                </td>
                <td style="text-align: left;">
                    <h3 style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5;">
                        LAPORAN REKAP KARYAWAN {{ mb_strtoupper($namabulan[$bulan] ?? '') }} {{ $tahun }}<br>
                        NINJA XPRESS GBN BANYUWANGI<br>
                    </h3>
                </td>
            </tr>
        </table>

        <!-- Tabel Presensi -->
        <table class="tablepresensi">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Karyawan</th>
                    <th>Nama Karyawan</th>
                    <th>Jumlah Hadir</th>
                    <th>Jumlah Terlambat</th>
                    <th>Jumlah Sakit</th>
                    <th>Jumlah Izin</th>
                    <th>Jumlah Belum Absen</th>
                    <th>Jumlah Belum Absen Pulang</th>
                    <th>Jumlah Lembur</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekap as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $r->id_karyawan }}</td>
                    <td style="text-align: left;">{{ $r->nama }}</td>
                    <td>{{ $r->jumlah_hadir }}</td>
                    <td>{{ $r->jumlah_terlambat }}</td>
                    <td>{{ $r->jumlah_sakit }}</td>
                    <td>{{ $r->jumlah_izin }}</td>
                    <td>{{ $r->jumlah_belum_absen }}</td>
                    <td>{{ $r->jumlah_belum_absen_pulang }}</td>
                    <td>{{ $r->jumlah_jam_tambahan }} Jam</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align: center;">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda tangan -->
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
