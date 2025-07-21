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

    public function pengajuanUpdateProfil()
    {
        $pengajuan = DB::table('pengajuan_update_profil')
            ->join('karyawan', 'pengajuan_update_profil.id_karyawan', '=', 'karyawan.id_karyawan')
            ->select('pengajuan_update_profil.*', 'karyawan.nama as nama_lama')
            ->orderBy('pengajuan_update_profil.created_at', 'desc')
            ->get();

        return view('admin.karyawan.pengajuanprofil', compact('pengajuan'));
    }

    public function verifikasiPengajuan(Request $request, $id)
    {
        $pengajuan = DB::table('pengajuan_update_profil')->where('id', $id)->first();

        if (!$pengajuan) {
            return back()->with('warning', 'Pengajuan tidak ditemukan.');
        }

        if ($request->aksi == 'setuju') {
            $data = [];

            if (!is_null($pengajuan->nama)) {
                $data['nama'] = $pengajuan->nama;
            }

            if (!is_null($pengajuan->no_hp)) {
                $data['no_hp'] = $pengajuan->no_hp;
            }

            if (!is_null($pengajuan->jabatan)) {
                $data['jabatan'] = $pengajuan->jabatan;
            }

            if (!is_null($pengajuan->password)) {
                $data['password'] = $pengajuan->password;
            }

            if (!is_null($pengajuan->foto)) {
                $data['foto'] = $pengajuan->foto;
            }

            if (empty($data)) {
                return back()->with('warning', 'Tidak ada data yang dapat diperbarui.');
            }

            DB::table('karyawan')->where('id_karyawan', $pengajuan->id_karyawan)->update($data);
            DB::table('pengajuan_update_profil')->where('id', $id)->update(['status' => 'disetujui']);
        } else {
            DB::table('pengajuan_update_profil')->where('id', $id)->update(['status' => 'ditolak']);
        }

        return back()->with('success', 'Pengajuan telah diverifikasi.');
    }

}
