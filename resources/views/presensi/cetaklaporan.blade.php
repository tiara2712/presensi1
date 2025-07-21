<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">
    <style>
        @page {
            size: A4 landscape;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        .header {
            text-align: center;
        }
    </style>
</head>
<body class="A4">
    <section class="sheet padding-10mm">
        <table style="width: 100%;">
            <tr>
                <td style="width: 200px;">
                    <img src="{{ public_path('assets/img/login/logo.png') }}" width="180" height="75" alt="">
                </td>
                <td style="text-align: left;">
                    <h3 style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5;">
                        LAPORAN PRESENSI KARYAWAN <br>
                        {{ $tanggal_awal_formatted }} s/d {{ $tanggal_akhir_formatted }}<br>
                        NINJA XPRESS GBN BANYUWANGI<br>
                    </h3>
                </td>
            </tr>
        </table>

        @if (count($rekap) > 0)
            <table style="margin-top: 15px; margin-bottom: 15px; font-family: Arial, sans-serif; font-size: 13px;">
                <tr>
                    <td style="width: 100px; vertical-align: top;">
                        @php
                            $fotoPath = !empty($rekap[0]->foto)
                                ? public_path('storage/uploads/karyawan/' . $rekap[0]->foto)
                                : public_path('assets/img/sample/avatar/avatar1.jpg');
                        @endphp
                        <img src="{{ $fotoPath }}" alt="Foto Karyawan"
                            style="width: 85px; height: 85px; object-fit: cover; border: 1px solid #ccc;">
                    </td>
                    <td style="padding-left: 20px; vertical-align: top;">
                        <table style="font-size: 13px; line-height: 1.6;">
                            <tr>
                                <td style="white-space: nowrap;">ID Karyawan</td>
                                <td style="padding: 0 5px;">:</td>
                                <td><strong>{{ $rekap[0]->id_karyawan }}</strong></td>
                            </tr>
                            <tr>
                                <td style="white-space: nowrap;">Nama Karyawan</td>
                                <td style="padding: 0 5px;">:</td>
                                <td><strong>{{ $rekap[0]->nama }}</strong></td>
                            </tr>
                            <tr>
                                <td style="white-space: nowrap;">Jabatan</td>
                                <td style="padding: 0 5px;">:</td>
                                <td><strong>{{ $rekap[0]->jabatan ?? '-' }}</strong></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Hadir</th>
                    <th>Terlambat</th>
                    {{-- <th>Izin</th>
                    <th>Sakit</th> --}}
                    <th>Belum Absen</th>
                    <th>Belum Absen Pulang</th>
                    <th>Jam Lembur</th>
                    <th>IP Karyawan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekap as $index => $r)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $r->jumlah_hadir }}</td>
                        <td>{{ $r->jumlah_terlambat }}</td>
                        {{-- <td>{{ $r->jumlah_izin }}</td>
                        <td>{{ $r->jumlah_sakit }}</td> --}}
                        <td>{{ $r->jumlah_belum_absen }}</td>
                        <td>{{ $r->jumlah_belum_absen_pulang }}</td>
                        <td>{{ $r->jumlah_jam_tambahan }} jam</td>
                        <td>{{ $r->ip_address }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">Tidak ada data presensi</td>
                    </tr>
                @endforelse
            </tbody>
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
