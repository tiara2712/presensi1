@extends('layouts.presensi')
@section('header')
    <div class="appHeader bg-danger text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Data Perizinan</div>
        <div class="right"></div>
    </div>
@endsection

@section('content')
    <div class="row" style="margin-top: 70px">
        <div class="col">
            @php
                $messagesuccess = Session::get('success');
                $messageerror = Session::get('error');
            @endphp
            @if ($messagesuccess)
                <div class="alert alert-success">
                    {{ $messagesuccess }}
                </div>
            @endif
            @if ($messageerror)
                <div class="alert alert-danger">
                    {{ $messageerror }}
                </div>
            @endif
        </div>
    </div>

    <div class="row px-8">
        <div class="col">
            <div class="table-responsive bg-white p-6 rounded shadow-sm">
                <table class="table table-bordered table-striped text-center">
                    <thead class="table-secondary">
                        <tr>
                            <th>Tanggal Izin</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataizin as $d)
                            <tr>
                                <td>{{ date("d-m-Y", strtotime($d->tgl_izin)) }}</td>
                                <td>({{ $d->status == 's' ? 'Sakit' : 'Izin' }})</td>
                                <td>
                                    @if ($d->status_approved == 0)
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif ($d->status_approved == 1)
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif ($d->status_approved == 2)
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="fab-button bottom-right" style="margin-bottom: 70px">
        <a href="/buatizin" class="fab">
            <ion-icon name="add-outline"></ion-icon>
        </a>
    </div>
@endsection
