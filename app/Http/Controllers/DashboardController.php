<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     $karyawan = Auth::guard('karyawan')->user();
    //     $id_karyawan = $karyawan->id_karyawan;
    //     $hariini = date("Y-m-d");
    //     $bulanini = date("m") * 1;
    //     $tahunini = date("Y");
    //     $presensihariini = DB::table('presensi')->where('id_karyawan', $id_karyawan)->where('tgl_presensi', $hariini)->first();
    //     $historibulanini = DB::table('presensi')->whereRaw('MONTH(tgl_presensi)="'.$bulanini. '"')->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')->where('id_karyawan', $id_karyawan)->orderBy('tgl_presensi')->get();
    //     $rekappresensi = DB::table('presensi')->selectRaw('COUNT(id_karyawan) as jmlhadir, SUM(IF(jam_in > "09:00", 1 ,0)) as jmlterlambat')->where('id_karyawan', $id_karyawan)->whereRaw('MONTH(tgl_presensi)="'.$bulanini. '"')->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')->first();
    //     // dd($rekappresensi);
    //     $namabulan = ["","Januari", "Februari", " Maret", "April", "Mei", "Juni", "Juli", "Agustus", " September", "Oktober", "November", "Desember"];

    //     $rekapizin = DB::table('perizinan')
    //         ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin,SUM(IF(status="s",1,0)) as jmlsakit')
    //         ->where('id_karyawan', $id_karyawan)->whereRaw('MONTH(tgl_izin)="'.$bulanini. '"')
    //         ->whereRaw('YEAR(tgl_izin)="' . $tahunini . '"')
    //         ->where('status_approved', 1)
    //         ->first();

    //     return view('dashboard.dashboard', compact('karyawan', 'presensihariini', 'historibulanini', 'namabulan', 'bulanini', 'tahunini', 'rekappresensi', 'rekapizin'));

    // }

    public function index()
    {
        $karyawan = Auth::guard('karyawan')->user();
        $id_karyawan = $karyawan->id_karyawan;
        $hariini = date("Y-m-d");
        $bulanini = date("m") * 1;
        $tahunini = date("Y");

        $presensihariini = DB::table('presensi')
            ->where('id_karyawan', $id_karyawan)
            ->where('tgl_presensi', $hariini)
            ->first();

        $historibulanini = DB::table('presensi')
            ->whereRaw('MONTH(tgl_presensi) = ?', [$bulanini])
            ->whereRaw('YEAR(tgl_presensi) = ?', [$tahunini])
            ->where('id_karyawan', $id_karyawan)
            ->orderBy('tgl_presensi')
            ->get();

        $rekappresensi = DB::table('presensi')
            ->selectRaw('
                COALESCE(SUM(IF(jam_in IS NOT NULL AND jam_out IS NOT NULL, 1, 0)), 0) as jmlhadir,
                COALESCE(SUM(IF(jam_in > "09:00", 1 ,0)), 0) as jmlterlambat,
                COALESCE(SUM(IF(jam_in IS NOT NULL AND jam_out IS NULL, 1, 0)), 0) as jmlbelumpulang
            ')
            ->where('id_karyawan', $id_karyawan)
            ->whereRaw('MONTH(tgl_presensi) = ?', [$bulanini])
            ->whereRaw('YEAR(tgl_presensi) = ?', [$tahunini])
            ->first();

        $rekapizin = DB::table('perizinan')
            ->selectRaw('
                SUM(IF(status = "i", 1, 0)) as jmlizin,
                SUM(IF(status = "s", 1, 0)) as jmlsakit
            ')
            ->where('id_karyawan', $id_karyawan)
            ->whereRaw('MONTH(tgl_izin) = ?', [$bulanini])
            ->whereRaw('YEAR(tgl_izin) = ?', [$tahunini])
            ->where('status_approved', 1)
            ->first();

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        return view('dashboard.dashboard', compact('karyawan','presensihariini','historibulanini','namabulan','bulanini','tahunini','rekappresensi','rekapizin'));
    }

    // public function dashboardadmin()
    // {
    //     $hariini = date("Y-m-d");
    //     $rekappresensi = DB::table('presensi')
    //         ->selectRaw('COUNT(id_karyawan) as jmlhadir, SUM(IF(jam_in > "09:00", 1 ,0)) as jmlterlambat')
    //         ->where('tgl_presensi', $hariini)
    //         ->first();

    //     $rekapizin = DB::table('perizinan')
    //         ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin,SUM(IF(status="s",1,0)) as jmlsakit')
    //         ->where('tgl_izin', $hariini)
    //         ->where('status_approved', 1)
    //         ->first();

    //     return view('dashboard.admin.dashboardadmin', compact('rekappresensi', 'rekapizin'));
    // }

    public function dashboardadmin()
    {
        $hariini = date("Y-m-d");
        $jmlkaryawan = DB::table('karyawan')->count();

        $rekappresensi = DB::table('presensi')
            ->selectRaw('COUNT(id_karyawan) as jmlhadir, SUM(IF(jam_in > "09:00", 1 ,0)) as jmlterlambat')
            ->where('tgl_presensi', $hariini)
            ->first();

        $rekapizin = DB::table('perizinan')
            ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin, SUM(IF(status="s",1,0)) as jmlsakit')
            ->where('tgl_izin', $hariini)
            ->where('status_approved', 1)
            ->first();

        $jmlhadir = $rekappresensi->jmlhadir ?? 0;
        $jmlizin = $rekapizin->jmlizin ?? 0;
        $jmlsakit = $rekapizin->jmlsakit ?? 0;

        $rekappresensi->jmlbelumabsen = $jmlkaryawan - ($jmlhadir + $jmlizin + $jmlsakit);

        return view('dashboard.admin.dashboardadmin', compact('rekappresensi', 'rekapizin'));
    }
}
