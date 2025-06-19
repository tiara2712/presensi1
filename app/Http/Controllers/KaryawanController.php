<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::query();
        $query->select('karyawan.*');
        $query->orderBy('nama');
        if(!empty($request->nama)) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }
        $karyawan = $query->paginate(10);

        return view('dashboard.karyawan.index', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $id_karyawan = $request->id_karyawan;
        $nama = $request->nama;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;
        $password = Hash::make($request->id_karyawan);

        try {
            DB::table('karyawan')->insert([
                'id_karyawan' => $request->id_karyawan,
                'nama' => $request->nama,
                'jabatan' => $request->jabatan,
                'no_hp' => $request->no_hp,
                'password' => Hash::make($request->id_karyawan)
            ]);

            return redirect()->back()->with('success', 'Data Berhasil Disimpan');
        } catch (\Exception $e) {
            return redirect()->back()->with('warning', 'Data Gagal Disimpan');
        }
    }

    public function edit(Request $request)
    {
        $id_karyawan = $request->id_karyawan;
        $karyawan = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
        return view('dashboard.karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request)
    {
        $id_karyawan = $request->id_karyawan;

        try {
            $data = [
                'nama' => $request->nama,
                'jabatan' => $request->jabatan,
                'no_hp' => $request->no_hp,
                'password' => Hash::make($id_karyawan)
            ];

            DB::table('karyawan')->where('id_karyawan', $id_karyawan)->update($data);

            return redirect()->back()->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->with('warning', 'Data Gagal Diupdate');
        }
    }

    public function delete($id_karyawan)
    {
        $delete = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->delete();
        if ($delete) {
            return Redirect::back()->with(['success' => 'Data Berhasil di Hapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal di Hapus']);
        }
    }

}
