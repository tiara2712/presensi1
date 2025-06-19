<style>
    .badge {
        padding: 0.4em 0.8em;
        margin: 0 5px;
        display: inline-block;
        min-width: 100px;
        font-size: 0.9em;
        text-align: center;
    }
    table td, table th {
        padding: 10px 15px;
        vertical-align: middle;
    }
</style>

@if ($rekap->isEmpty())
    <div class="alert alert-outline-warning text-center">
        <p>Data Belum Ada</p>
    </div>
@else
    <table class="table table-bordered table-striped text-center bg-white">
        <thead class="bg-white text-dark">
            <tr>
                <th>Tanggal Presensi</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rekap as $d)
                @php
                    $jamIn = $d->jam_in ? strtotime($d->jam_in) : null;
                    $jamOut = $d->jam_out ? strtotime($d->jam_out) : null;

                    if (!$jamIn) {
                        $badgeIn = '<span class="badge badge-primary">Belum Absen</span>';
                    } elseif ($jamIn < strtotime('08:00:00')) {
                        $badgeIn = '<span class="badge badge-warning">' . $d->jam_in . '</span>';
                    } elseif ($jamIn >= strtotime('08:00:00') && $jamIn <= strtotime('09:00:00')) {
                        $badgeIn = '<span class="badge badge-success">' . $d->jam_in . '</span>';
                    } else {
                        $badgeIn = '<span class="badge badge-danger">' . $d->jam_in . '</span>';
                    }

                    if (!$jamOut) {
                        $badgeOut = '<span class="badge badge-primary">Belum Absen</span>';
                    } elseif ($jamOut < strtotime('17:00:00') || $jamOut > strtotime('23:59:59')) {
                        $badgeOut = '<span class="badge badge-warning">' . $d->jam_out . '</span>';
                    } else {
                        $badgeOut = '<span class="badge badge-success">' . $d->jam_out . '</span>';
                    }
                @endphp

                <tr>
                    <td><b>{{ date("d-m-Y", strtotime($d->tgl_presensi)) }}</b></td>
                    <td>{!! $badgeIn !!}</td>
                    <td>{!! $badgeOut !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
