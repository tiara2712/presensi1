@php
    function selisih($jam_masuk, $jam_keluar)
    {
        list($h, $m, $s) = explode(":", $jam_masuk);
        $dtAwal = mktime($h, $m, $s, 1, 1, 1);
        list($h2, $m2, $s2) = explode(":", $jam_keluar);
        $dtAkhir = mktime($h2, $m2, $s2, 1, 1, 1);
        $dtSelisih = $dtAkhir - $dtAwal;
        $totalmenit = $dtSelisih / 60;
        $jam = explode(".", $totalmenit / 60);
        $sisamenit = ($totalmenit / 60) - $jam[0];
        $sisamenit2 = $sisamenit * 60;
        return $jam[0] . ":" . round($sisamenit2);
    }
@endphp

@foreach ($presensi as $d)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $d->id_karyawan }}</td>
        <td>{{ $d->nama }}</td>
        <td>{{ $d->jabatan }}</td>
        <td>
            @if ($d->jam_in)
                @if ($d->jam_in < '08:00:00')
                    <span class="badge bg-danger">{{ $d->jam_in }}</span>
                @elseif ($d->jam_in > '09:00:00')
                    <span class="badge bg-danger">{{ $d->jam_in }}</span>
                @else
                    <span class="badge bg-success">{{ $d->jam_in }}</span>
                @endif
            @else
                <span class="badge bg-danger">Belum Absen</span>
            @endif
        </td>

        <td>
            @if ($d->foto_in)
                <img src="{{ Storage::url('uploads/absensi/'.$d->foto_in) }}" class="avatar" alt="" width="40">
            @else
                <i class="bx bx-timer text-danger fs-5"></i>
            @endif
        </td>

        <td>
            @if ($d->jam_out)
                @if ($d->jam_out < '17:00:00' || $d->jam_out > '23:59:59')
                    <span class="badge bg-danger">{{ $d->jam_out }}</span>
                @else
                    <span class="badge bg-success">{{ $d->jam_out }}</span>
                @endif
            @else
                <span class="badge bg-danger">Belum Absen</span>
            @endif
        </td>

        <td>
            @if ($d->foto_out)
                <img src="{{ Storage::url('uploads/absensi/'.$d->foto_out) }}" class="avatar" alt="" width="40">
            @else
                <i class="bx bx-timer text-danger fs-5"></i>
            @endif
        </td>

        <td>
            @if ($d->jam_in && $d->jam_in > '09:00:00')
                @php $jamterlambat = selisih('09:00:00', $d->jam_in); @endphp
                <span class="badge bg-danger">Terlambat {{ $jamterlambat }}</span>
            @elseif ($d->jam_in && $d->jam_in >= '08:00:00' && $d->jam_in <= '09:00:00')
                <span class="badge bg-success">Tepat Waktu</span>
            @elseif ($d->jam_in && $d->jam_in < '08:00:00')
                <span class="badge bg-warning">Terlalu Awal</span>
            @else
                <span class="badge bg-secondary">Belum Absen</span>
            @endif
        </td>
    </tr>
@endforeach
