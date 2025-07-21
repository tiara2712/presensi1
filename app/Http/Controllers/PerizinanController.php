<?php

namespace App\Http\Controllers;

use App\Models\Perizinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PerizinanController extends Controller
{
    public function izin()
    {
        $id_karyawan  = Auth::guard('karyawan')->user()->id_karyawan;
        $dataizin = DB::table('perizinan')->where('id_karyawan', $id_karyawan)->get();
        return view('dashboard.izin', compact('dataizin'));
    }

    public function buatizin(Request $request)
    {
        return view('dashboard.buatizin');
    }

    public function upload(Request $request)
    {
        $id_karyawan = Auth::guard('karyawan')->user()->id_karyawan;

        $tgl_izin = date('Y-m-d', strtotime($request->tgl_izin));
        $status = $request->status;
        $keterangan = $request->keterangan;
        $fileName = null;

        if ($status === 's') {
            if (!$request->hasFile('file')) {
                return redirect()->back()->with('error', 'File PDF wajib diupload untuk sakit');
            }

            $ext = $request->file('file')->getClientOriginalExtension();
            if (strtolower($ext) !== 'pdf') {
                return redirect()->back()->with('error', 'File harus berupa PDF');
            }

            $fileName = $id_karyawan . "_" . date('Ymd_His') . "." . $ext;
            $folderPath = "public/uploads/perizinan/";
            $request->file('file')->storeAs($folderPath, $fileName);
        }

        $data = [
            'id_karyawan' => $id_karyawan,
            'tgl_izin' => $tgl_izin,
            'status' => $status,
            'keterangan' => $status === 'i' ? $keterangan : null,
            'file' => $fileName
        ];

        $simpan = DB::table('perizinan')->insert($data);

        if ($simpan) {
            return redirect('/izin')->with('success', 'Pengajuan berhasil disimpan');
        } else {
            return redirect('/izin')->with('error', 'Pengajuan gagal disimpan');
        }
    }

    // public function upload(Request $request)
    // {
    //     $id_karyawan = Auth::guard('karyawan')->user()->id_karyawan;

    //     $tgl_izin = date('Y-m-d', strtotime($request->tgl_izin));
    //     $status = $request->status;
    //     $keterangan = $request->keterangan;
    //     $fileName = null;

    //     if ($request->hasFile('file')) {
    //         $ext = $request->file('file')->getClientOriginalExtension();
    //         $fileName = $id_karyawan . "_" . date('Ymd_His') . "." . $ext;
    //         $folderPath = "public/uploads/perizinan/";
    //         $request->file('file')->storeAs($folderPath, $fileName);
    //     }

    //     $data = [
    //         'id_karyawan' => $id_karyawan,
    //         'tgl_izin' => $tgl_izin,
    //         'status' => $status,
    //         'keterangan' => $keterangan,
    //         'file' => $fileName
    //     ];

    //     $simpan = DB::table('perizinan')->insert($data);

    //     if ($simpan) {
    //         return redirect('/izin')->with(['success' => 'Data berhasil disimpan']);
    //     } else {
    //         return redirect('/izin')->with(['error' => 'Data gagal disimpan']);
    //     }
    // }

    public function izinsakit(Request $request)
    {
        $query = Perizinan::query();
        $query->select('id', 'tgl_izin', 'perizinan.id_karyawan', 'nama', 'jabatan', 'status', 'file', 'keterangan', 'status_approved');
        $query->join('karyawan', 'perizinan.id_karyawan', '=', 'karyawan.id_karyawan');
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $query->whereBetween('tgl_izin', [$request->start_date, $request->end_date]);
        }
        if (!empty($request->id_karyawan)) {
            $query->where('perizinan.id_karyawan', $request->id_karyawan);
        }
        if (!empty($request->nama)) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }
        if ($request->status_approved == "0" || $request->status_approved == "1" || $request->status_approved == "2") {
            $query->where('status_approved', $request->status_approved);
        }
        $query->orderBy('tgl_izin', 'desc');
        $izinsakit = $query->paginate(5);
        $izinsakit->appends($request->all());
        return view('presensi.izinsakit', compact('izinsakit'));
    }

    public function approvedizinsakit(Request $request)
    {
        $status_approved = $request->status_approved;
        $id_izinsakit_form = $request->id_izinsakit_form;
        $update = DB::table('perizinan')->where('id', $id_izinsakit_form)->update([
            'status_approved' => $status_approved
        ]);
        if ($update) {
            return Redirect::back()->with(['success' => ' Data Berhasil di Update']);
        } else {
            return Redirect::back()->with(['warning' => ' Data Gagal di Update']);
        }
    }

    public function batalkanizinsakit($id)
    {
        $update = DB::table('perizinan')->where('id', $id)->update([
            'status_approved' => 0
        ]);
        if ($update) {
            return Redirect::back()->with(['success' => ' Data Berhasil di Update']);
        } else {
            return Redirect::back()->with(['warning' => ' Data Gagal di Update']);
        }
    }

    public function cekperizinan(Request $request) {
        $tgl_izin = $request->tgl_izin;
        $id_karyawan = Auth::guard('karyawan')->user()->id_karyawan;

        $cek = DB::table('perizinan')->where('id_karyawan', $id_karyawan)->where('tgl_izin', $tgl_izin)->count();
        return $cek;
    }
}
