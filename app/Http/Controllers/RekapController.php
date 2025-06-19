<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RekapController extends Controller
{
    public function rekap()
    {
        $rekap = collect();
        // $namabulan = ["", "Januari", "Februari", " Maret", "April", "Mei", "Juni", "Juli", "Agustus", " September", "Oktober", "November", "Desember"];
        return view('dashboard.rekap', compact('rekap'));
    }

    // public function getrekap(Request $request)
    // {
    //     $bulan = $request->bulan;
    //     $tahun = $request->tahun;
    //     $id_karyawan = Auth::guard('karyawan')->user()->id_karyawan;

    //     $rekap = DB::table('presensi')->whereRaw('MONTH(tgl_presensi)="'.$bulan. '"')->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')->where('id_karyawan', $id_karyawan)->orderBy('tgl_presensi')->get();

    //     return view('dashboard.getrekap', compact('rekap'));
    // }

    // public function getrekap(Request $request)
    // {
        // $request->validate([
        //     'start_date' => 'required|date',
        //     'end_date'   => 'required|date|after_or_equal:start_date',
        // ]);

        // $id_karyawan = Auth::guard('karyawan')->user()->id_karyawan;

        // $query = DB::table('presensi')
        //     ->where('id_karyawan', $id_karyawan)
        //     ->whereBetween('tgl_presensi', [$request->start_date, $request->end_date])
        //     ->orderBy('tgl_presensi');

        // if ($request->filled('status')) {
        //     $query->where('status', $request->status);
        // }

        // $rekap = $query->get();

    //     return view('dashboard.getrekap', compact('rekap'));
    // }

    public function getrekap(Request $request)
    {

        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $id_karyawan = Auth::guard('karyawan')->user()->id_karyawan;

        $query = DB::table('presensi')
            ->where('id_karyawan', $id_karyawan)
            ->whereBetween('tgl_presensi', [$request->start_date, $request->end_date])
            ->orderBy('tgl_presensi');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rekap = $query->get();

        // $dataizin = DB::table('perizinan')->where('id_karyawan', $id_karyawan)->get();

        return view('dashboard.getrekap', compact('rekap'));
    }



}
