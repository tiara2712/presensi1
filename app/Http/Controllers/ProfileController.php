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

    // public function updateprofile(Request $request)
    // {
    //     $id_karyawan = Auth::guard('karyawan')->user()->id_karyawan;
    //     $nama = $request->nama;
    //     $no_hp = $request->no_hp;
    //     $jabatan = $request->jabatan;
    //     $karyawan = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
    //     if ($request->hasFile('foto')) {
    //         $foto = $id_karyawan . "." . $request->file('foto')->getClientOriginalExtension();
    //     } else {
    //         $foto = $karyawan->foto;
    //     }
    //     $password = Hash::make($request->password);

    //     if (empty($request->password)) {
    //         $data = [
    //             'nama' => $nama,
    //             'no_hp' => $no_hp,
    //             'jabatan' => $jabatan,
    //             'foto' => $foto
    //         ];
    //     } else {
    //         $data = [
    //             'nama' => $nama,
    //             'no_hp' => $no_hp,
    //             'jabatan' => $jabatan,
    //             'password' => $password,
    //             'foto' => $foto
    //         ];
    //     }

    //     $update = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->update($data);
    //     if ($update) {
    //         if ($request->hasFile('foto')) {
    //             $folderPath = "public/uploads/karyawan/";
    //             $request->file('foto')->storeAs($folderPath, $foto);
    //         }
    //         return Redirect::back()->with(['success' => 'Data Berhasil di Update']);
    //     } else {
    //         return Redirect::back()->with(['Error' => 'Data Gagal di Update']);
    //     }
    // }

    public function updateprofile(Request $request)
    {
        $id_karyawan = Auth::guard('karyawan')->user()->id_karyawan;

        $karyawan = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();

        $pending = DB::table('pengajuan_update_profil')
            ->where('id_karyawan', $id_karyawan)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return Redirect::back()->with(['error' => 'Anda masih memiliki pengajuan yang menunggu persetujuan.']);
        }

        $data = [
            'id_karyawan' => $id_karyawan,
            'status' => 'pending',
            'created_at' => now(),
        ];

        if ($request->nama != $karyawan->nama) {
            $data['nama'] = $request->nama;
        }

        if ($request->no_hp != $karyawan->no_hp) {
            $data['no_hp'] = $request->no_hp;
        }

        if ($request->jabatan != $karyawan->jabatan) {
            $data['jabatan'] = $request->jabatan;
        }

        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            $foto = $id_karyawan . "_pengajuan." . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->storeAs('public/uploads/karyawan/', $foto);
            $data['foto'] = $foto;
        }

        if (count($data) <= 3) {
            return Redirect::back()->with(['error' => 'Tidak ada perubahan yang diajukan.']);
        }

        DB::table('pengajuan_update_profil')->insert($data);

        return Redirect::back()->with(['success' => 'Pengajuan berhasil dikirim. Menunggu persetujuan admin.']);
    }

}
