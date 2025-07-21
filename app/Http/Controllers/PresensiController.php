<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Karyawan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function create()
    {
        $hariini = date("Y-m-d");
        $id_karyawan = Auth::guard('karyawan')->user()->id_karyawan;
        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('id_karyawan', $id_karyawan)->count();
        return view('presensi.create', compact('cek'));
    }

    public function store(Request $request)
    {
        $ipClient = $request->ip();

        if (!str_starts_with(trim($ipClient), '10.252.68.')) {
            return response("error|Gagal, maaf anda tidak menggunakan jaringan kantor. IP Anda: $ipClient", 403);
        }

        // // Jika IP ngrok, set ipClient menjadi IP kantor default untuk disimpan
        // if (in_array($ipClient, $ipNgrokWhitelist)) {
        //     $ipClient = '192.168.1.99'; // contoh default IP kantor
        // }

        $id_karyawan = Auth::guard('karyawan')->user()->id_karyawan;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");
        $image = $request->image;

        $folderPath = "public/uploads/absensi/";
        $formatName = $id_karyawan . "-" . $tgl_presensi;
        $image_parts = explode(";base64,", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        $cek = DB::table('presensi')
            ->where('tgl_presensi', $tgl_presensi)
            ->where('id_karyawan', $id_karyawan)
            ->count();

        if ($cek > 0) {
            $data_pulang = [
                'jam_out' => $jam,
                'foto_out' => $fileName,
                'ip_address' => $ipClient
            ];

            $update = DB::table('presensi')
                ->where('tgl_presensi', $tgl_presensi)
                ->where('id_karyawan', $id_karyawan)
                ->update($data_pulang);

            if ($update) {
                Storage::put($file, $image_base64);
                return "success|Terima kasih, hati-hati di jalan";
            } else {
                return "error|Gagal absen pulang";
            }
        } else {
            $data = [
                'id_karyawan' => $id_karyawan,
                'tgl_presensi' => $tgl_presensi,
                'jam_in' => $jam,
                'foto_in' => $fileName,
                'ip_address' => $ipClient
            ];

            $simpan = DB::table('presensi')->insert($data);

            if ($simpan) {
                Storage::put($file, $image_base64);
                return "success|Absen masuk berhasil";
            } else {
                return "error|Gagal absen masuk";
            }
        }
    }

    public function getpresensi(Request $request)
    {
        $tanggal = $request->tanggal;

        $presensi = DB::table('karyawan')
            ->leftJoin('presensi', function ($join) use ($tanggal) {
                $join->on('karyawan.id_karyawan', '=', 'presensi.id_karyawan')
                    ->where('presensi.tgl_presensi', '=', $tanggal);
            })
            ->select(
                'karyawan.id_karyawan',
                'karyawan.nama',
                'karyawan.jabatan',
                'presensi.jam_in',
                'presensi.jam_out',
                'presensi.foto_in',
                'presensi.foto_out',
                'presensi.tgl_presensi'
            )
            ->orderBy('karyawan.nama')
            ->get();

        return view('presensi.getpresensi', compact('presensi'));
    }

    public function laporan(Request $request)
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = DB::table('karyawan')->orderBy('nama')->get();
        $rekap = [];

        if ($request->start_date && $request->end_date && $request->id_karyawan) {
            $tanggal_awal = Carbon::parse($request->start_date)->startOfDay();
            $tanggal_akhir = Carbon::parse($request->end_date)->endOfDay();

            $daftarTanggal = [];
            for ($date = $tanggal_awal->copy(); $date->lte($tanggal_akhir); $date->addDay()) {
                $daftarTanggal[] = $date->toDateString();
            }

            $k = DB::table('karyawan')->where('id_karyawan', $request->id_karyawan)->first();

            $jumlah_hadir = 0;
            $jumlah_terlambat = 0;
            $jumlah_izin = 0;
            $jumlah_sakit = 0;
            $jumlah_belum_absen = 0;
            $jumlah_belum_absen_pulang = 0;
            $jumlah_jam_tambahan = 0;

            foreach ($daftarTanggal as $tanggal) {
                $presensi = DB::table('presensi')
                    ->where('id_karyawan', $k->id_karyawan)
                    ->whereDate('tgl_presensi', $tanggal)
                    ->first();

                $izin = DB::table('perizinan')
                    ->where('id_karyawan', $k->id_karyawan)
                    ->whereDate('tgl_izin', $tanggal)
                    ->where('status_approved', 1)
                    ->first();

                if ($presensi && $presensi->jam_in && $presensi->jam_out) {
                    if ($presensi->jam_in <= '09:00:00') {
                        $jumlah_hadir++;
                    } else {
                        $jumlah_terlambat++;
                    }

                    if ($presensi->jam_out > '17:00:00') {
                        try {
                            $jam_out = Carbon::createFromFormat('H:i:s', $presensi->jam_out);
                            $batas_awal = Carbon::createFromFormat('H:i:s', '17:00:00');
                            $jumlah_jam_tambahan += $jam_out->diffInHours($batas_awal);
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                } elseif ($presensi && $presensi->jam_in && !$presensi->jam_out) {
                    $jumlah_belum_absen_pulang++;
                }

                if ($izin) {
                    if ($izin->status === 'i') {
                        $jumlah_izin++;
                    } elseif ($izin->status === 's') {
                        $jumlah_sakit++;
                    }
                }

                if (!$presensi && !$izin) {
                    $jumlah_belum_absen++;
                }
            }

            $presensi_terakhir = DB::table('presensi')
                ->where('id_karyawan', $k->id_karyawan)
                ->orderByDesc('tgl_presensi')
                ->first();

            $ip_terakhir = $presensi_terakhir ? $presensi_terakhir->ip_address : '-';

            $rekap[] = (object) [
                'id_karyawan' => $k->id_karyawan,
                'nama' => $k->nama,
                'jumlah_hadir' => $jumlah_hadir,
                'jumlah_terlambat' => $jumlah_terlambat,
                'jumlah_izin' => $jumlah_izin,
                'jumlah_sakit' => $jumlah_sakit,
                'jumlah_belum_absen' => $jumlah_belum_absen,
                'jumlah_belum_absen_pulang' => $jumlah_belum_absen_pulang,
                'jumlah_jam_tambahan' => $jumlah_jam_tambahan,
                'ip_address' => $ip_terakhir,
            ];
        }

        return view("presensi.laporan", compact('namabulan', 'karyawan', 'rekap'));
    }

    public function cetaklaporan(Request $request)
    {
        $rekap = [];

        if ($request->start_date && $request->end_date && $request->id_karyawan) {
            $tanggal_awal = Carbon::parse($request->start_date)->startOfDay();
            $tanggal_akhir = Carbon::parse($request->end_date)->endOfDay();

            $tanggal_awal_formatted = $tanggal_awal->translatedFormat('d F Y');
            $tanggal_akhir_formatted = $tanggal_akhir->translatedFormat('d F Y');

            $daftarTanggal = [];
            for ($date = $tanggal_awal->copy(); $date->lte($tanggal_akhir); $date->addDay()) {
                $daftarTanggal[] = $date->toDateString();
            }

            $karyawan = DB::table('karyawan')
                ->where('id_karyawan', $request->id_karyawan)
                ->select('id_karyawan', 'nama', 'jabatan', 'foto')
                ->first();

            if (!$karyawan) {
                return back()->with('error', 'Karyawan tidak ditemukan.');
            }

            $jumlah_hadir = 0;
            $jumlah_terlambat = 0;
            $jumlah_izin = 0;
            $jumlah_sakit = 0;
            $jumlah_belum_absen = 0;
            $jumlah_belum_absen_pulang = 0;
            $jumlah_jam_tambahan = 0;

            foreach ($daftarTanggal as $tanggal) {
                $presensi = DB::table('presensi')
                    ->where('id_karyawan', $karyawan->id_karyawan)
                    ->whereDate('tgl_presensi', $tanggal)
                    ->first();

                $izin = DB::table('perizinan')
                    ->where('id_karyawan', $karyawan->id_karyawan)
                    ->whereDate('tgl_izin', $tanggal)
                    ->where('status_approved', 1)
                    ->first();

                if ($presensi && $presensi->jam_in && $presensi->jam_out) {
                    if ($presensi->jam_in <= '09:00:00') {
                        $jumlah_hadir++;
                    } else {
                        $jumlah_terlambat++;
                    }

                    if ($presensi->jam_out > '17:00:00') {
                        try {
                            $jam_out = Carbon::createFromFormat('H:i:s', $presensi->jam_out);
                            $batas_awal = Carbon::createFromFormat('H:i:s', '17:00:00');
                            $jumlah_jam_tambahan += $jam_out->diffInHours($batas_awal);
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                } elseif ($presensi && $presensi->jam_in && !$presensi->jam_out) {
                    $jumlah_belum_absen_pulang++;
                }

                if ($izin) {
                    if ($izin->status === 'i') {
                        $jumlah_izin++;
                    } elseif ($izin->status === 's') {
                        $jumlah_sakit++;
                    }
                }

                if (!$presensi && !$izin) {
                    $jumlah_belum_absen++;
                }
            }

            $presensi_terakhir = DB::table('presensi')
                ->where('id_karyawan', $karyawan->id_karyawan)
                ->orderByDesc('tgl_presensi')
                ->first();

            $ip_terakhir = $presensi_terakhir->ip_address ?? '-';

            $rekap[] = (object)[
                'id_karyawan' => $karyawan->id_karyawan,
                'nama' => $karyawan->nama,
                'jabatan' => $karyawan->jabatan,
                'foto' => $karyawan->foto,
                'jumlah_hadir' => $jumlah_hadir,
                'jumlah_terlambat' => $jumlah_terlambat,
                'jumlah_izin' => $jumlah_izin,
                'jumlah_sakit' => $jumlah_sakit,
                'jumlah_belum_absen' => $jumlah_belum_absen,
                'jumlah_belum_absen_pulang' => $jumlah_belum_absen_pulang,
                'jumlah_jam_tambahan' => $jumlah_jam_tambahan,
                'ip_address' => $ip_terakhir,
            ];
        } else {
            $tanggal_awal_formatted = '-';
            $tanggal_akhir_formatted = '-';
        }

        $pdf = Pdf::loadView('presensi.cetaklaporan', [
            'rekap' => $rekap,
            'tanggal_awal_formatted' => $tanggal_awal_formatted,
            'tanggal_akhir_formatted' => $tanggal_akhir_formatted,
        ])->setPaper('A4', 'landscape');

        return $pdf->stream('cetaklaporan.pdf');
    }

    public function laporanrekap(Request $request)
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawanQuery = Karyawan::query();

        if ($request->has('nama') && $request->nama) {
            $karyawanQuery->where('nama', 'like', '%' . $request->nama . '%');
        }

        $karyawan = $karyawanQuery->get();
        $rekap = [];

        $tanggal_awal = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $tanggal_akhir = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        $daftarTanggal = [];
        for ($date = $tanggal_awal->copy(); $date->lte($tanggal_akhir); $date->addDay()) {
            $daftarTanggal[] = $date->toDateString();
        }

        foreach ($karyawan as $k) {
            $jumlah_hadir = 0;
            $jumlah_terlambat = 0;
            $jumlah_izin = 0;
            $jumlah_sakit = 0;
            $jumlah_belum_absen = 0;
            $jumlah_belum_absen_pulang = 0;
            $jumlah_jam_tambahan = 0;

            foreach ($daftarTanggal as $tanggal) {
                $presensi = DB::table('presensi')
                    ->where('id_karyawan', $k->id_karyawan)
                    ->whereDate('tgl_presensi', $tanggal)
                    ->first();

                $izin = DB::table('perizinan')
                    ->where('id_karyawan', $k->id_karyawan)
                    ->whereDate('tgl_izin', $tanggal)
                    ->where('status_approved', 1)
                    ->first();

                if ($presensi && $presensi->jam_in && $presensi->jam_out) {
                    if ($presensi->jam_in <= '09:00:00') {
                        $jumlah_hadir++;
                    } else {
                        $jumlah_terlambat++;
                    }

                    if ($presensi->jam_out > '17:00:00') {
                        try {
                            $jam_out = Carbon::createFromFormat('H:i:s', $presensi->jam_out);
                            $batas_awal = Carbon::createFromFormat('H:i:s', '17:00:00');
                            $jumlah_jam_tambahan += $jam_out->diffInHours($batas_awal);
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                } elseif ($presensi && $presensi->jam_in && !$presensi->jam_out) {
                    $jumlah_belum_absen_pulang++;
                }

                if ($izin) {
                    if ($izin->status === 'i') {
                        $jumlah_izin++;
                    } elseif ($izin->status === 's') {
                        $jumlah_sakit++;
                    }
                }

                if (!$presensi && !$izin) {
                    $jumlah_belum_absen++;
                }
            }

            // $presensi_terakhir = DB::table('presensi')
            //     ->where('id_karyawan', $k->id_karyawan)
            //     ->orderByDesc('tgl_presensi')
            //     ->first();

            // $ip_terakhir = $presensi_terakhir ? $presensi_terakhir->ip_address : '-';

            $rekap[] = (object) [
                'id_karyawan' => $k->id_karyawan,
                'nama' => $k->nama,
                'jumlah_hadir' => $jumlah_hadir,
                'jumlah_terlambat' => $jumlah_terlambat,
                'jumlah_izin' => $jumlah_izin,
                'jumlah_sakit' => $jumlah_sakit,
                'jumlah_belum_absen' => $jumlah_belum_absen,
                'jumlah_belum_absen_pulang' => $jumlah_belum_absen_pulang,
                'jumlah_jam_tambahan' => $jumlah_jam_tambahan,
                // 'ip_address' => $ip_terakhir,
            ];
        }

        return view('presensi.laporanrekap', compact('rekap', 'namabulan'));
    }

    public function cetakPDF(Request $request)
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = Karyawan::all();
        $rekap = [];

        $tanggal_awal = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $tanggal_akhir = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        $tanggal_awal_formatted = $tanggal_awal->translatedFormat('d F Y');
        $tanggal_akhir_formatted = $tanggal_akhir->translatedFormat('d F Y');

        $daftarTanggal = [];
        for ($date = $tanggal_awal->copy(); $date->lte($tanggal_akhir); $date->addDay()) {
            $daftarTanggal[] = $date->toDateString();
        }

        foreach ($karyawan as $k) {
            $jumlah_hadir = 0;
            $jumlah_terlambat = 0;
            $jumlah_izin = 0;
            $jumlah_sakit = 0;
            $jumlah_belum_absen = 0;
            $jumlah_belum_absen_pulang = 0;
            $jumlah_jam_tambahan = 0;

            $tgl_presensi_pertama = DB::table('presensi')
                ->where('id_karyawan', $k->id_karyawan)
                ->orderBy('tgl_presensi', 'asc')
                ->value('tgl_presensi');

            $tgl_izin_pertama = DB::table('perizinan')
                ->where('id_karyawan', $k->id_karyawan)
                ->where('status_approved', 1)
                ->orderBy('tgl_izin', 'asc')
                ->value('tgl_izin');

            $mulai_absen = collect([$tgl_presensi_pertama, $tgl_izin_pertama])->filter()->min();

            if (!$mulai_absen) continue;

            foreach ($daftarTanggal as $tanggal) {
                if ($tanggal < $mulai_absen) continue;

                $presensi = DB::table('presensi')
                    ->where('id_karyawan', $k->id_karyawan)
                    ->whereDate('tgl_presensi', $tanggal)
                    ->first();

                $izin = DB::table('perizinan')
                    ->where('id_karyawan', $k->id_karyawan)
                    ->whereDate('tgl_izin', $tanggal)
                    ->where('status_approved', 1)
                    ->first();

                if ($presensi && $presensi->jam_in) {
                    if ($presensi->jam_in <= '09:00:00') {
                        $jumlah_hadir++;
                    } else {
                        $jumlah_terlambat++;
                    }

                    if (!$presensi->jam_out || $presensi->jam_out == '') {
                        $jumlah_belum_absen_pulang++;
                    }

                    if ($presensi->jam_out > '17:00:00') {
                        try {
                            $jam_out = Carbon::createFromFormat('H:i:s', $presensi->jam_out);
                            $batas_awal = Carbon::createFromFormat('H:i:s', '17:00:00');
                            $batas_akhir = Carbon::createFromFormat('H:i:s', '23:59:59');

                            if ($jam_out > $batas_akhir) {
                                $jam_out = $batas_akhir;
                            }

                            $selisih_jam = $jam_out->diffInHours($batas_awal);
                            $jumlah_jam_tambahan += $selisih_jam;
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }

                if ($izin) {
                    if ($izin->status === 'i') {
                        $jumlah_izin++;
                    } elseif ($izin->status === 's') {
                        $jumlah_sakit++;
                    }
                }

                if (!$presensi && !$izin) {
                    $jumlah_belum_absen++;
                }
            }

            $rekap[] = (object)[
                'id_karyawan' => $k->id_karyawan,
                'nama' => $k->nama,
                'jumlah_hadir' => $jumlah_hadir,
                'jumlah_terlambat' => $jumlah_terlambat,
                'jumlah_izin' => $jumlah_izin,
                'jumlah_sakit' => $jumlah_sakit,
                'jumlah_belum_absen' => $jumlah_belum_absen,
                'jumlah_belum_absen_pulang' => $jumlah_belum_absen_pulang,
                'jumlah_jam_tambahan' => $jumlah_jam_tambahan,
            ];
        }

        $pdf = \PDF::loadView('presensi.laporan_pdf', compact('rekap', 'namabulan', 'tanggal_awal_formatted', 'tanggal_akhir_formatted'));
        return $pdf->stream('laporan-rekap-presensi.pdf');
    }

}
