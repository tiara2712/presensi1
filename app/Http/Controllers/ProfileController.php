<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    public function editprofile()
    {
        $id_karyawan = Auth::guard('karyawan')->user()->id_karyawan;
        $karyawan = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
        // dd($karyawan);
        return view ('profile.editprofile', compact('karyawan'));
    }

    public function updateprofile(Request $request)
    {
        $id_karyawan = Auth::guard('karyawan')->user()->id_karyawan;
        $nama = $request->nama;
        $no_hp = $request->no_hp;
        $karyawan = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
        if ($request->hasFile('foto')) {
            $foto = $id_karyawan . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $karyawan->foto;
        }
        $password = Hash::make($request->password);

        if (empty($request->password)) {
            $data = [
                'nama' => $nama,
                'no_hp' => $no_hp,
                'foto' => $foto
            ];
        } else {
            $data = [
                'nama' => $nama,
                'no_hp' => $no_hp,
                'password' => $password,
                'foto' => $foto
            ];
        }

        $update = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->update($data);
        if ($update) {
            if ($request->hasFile('foto')) {
                $folderPath = "public/uploads/karyawan/";
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['success' => 'Data Berhasil di Update']);
        } else {
            return Redirect::back()->with(['Error' => 'Data Gagal di Update']);
        }
    }
}
