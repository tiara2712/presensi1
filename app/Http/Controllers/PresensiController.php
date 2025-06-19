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
        $ipClient = $request->header('X-Forwarded-For') ?? $request->ip();

        $ipKantor = '192.168.1.';

        $ipNgrokWhitelist = [
            '36.73.178.73',
        ];

        if (!str_starts_with($ipClient, $ipKantor) && !in_array($ipClient, $ipNgrokWhitelist)) {
            return response("error|Gagal, maaf anda tidak menggunakan jaringan kantor. IP Anda: $ipClient", 403);
        }

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
                'foto_out' => $fileName
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
                'foto_in' => $fileName
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

    // public function store(Request $request)
    // {
    //     $ssid = $request->ssid;
    //     $gateway_ip = $request->gateway_ip;

    //     if (empty($ssid) || empty($gateway_ip)) {
    //         return response("error|Data jaringan tidak terbaca", 400);
    //     }

    //     if ($ssid !== 'WiFi-Kantor' || $gateway_ip !== '192.168.1.1') {
    //         return response("error|Absen hanya bisa dari jaringan kantor", 403);
    //     }

    //     $id_karyawan = Auth::guard('karyawan')->user()->id_karyawan;
    //     $tgl_presensi = date("Y-m-d");
    //     $jam = date("H:i:s");
    //     $image = $request->image;
    //     $folderPath = "public/uploads/absensi/";
    //     $formatName = $id_karyawan . "-" . $tgl_presensi;
    //     $image_parts = explode(";base64,", $image);
    //     $image_base64 = base64_decode($image_parts[1]);
    //     $fileName = $formatName . ".png";
    //     $file = $folderPath . $fileName;

    //     $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)
    //                                 ->where('id_karyawan', $id_karyawan)
    //                                 ->count();

    //     if ($cek > 0) {
    //         $data_pulang = [
    //             'jam_out' => $jam,
    //             'foto_out' => $fileName
    //         ];
    //         $update = DB::table('presensi')
    //             ->where('tgl_presensi', $tgl_presensi)
    //             ->where('id_karyawan', $id_karyawan)
    //             ->update($data_pulang);

    //         if ($update) {
    //             Storage::put($file, $image_base64);
    //             return "success|Terima kasih, hati-hati di jalan";
    //         } else {
    //             return "error|Gagal absen pulang";
    //         }
    //     } else {
    //         $data = [
    //             'id_karyawan' => $id_karyawan,
    //             'tgl_presensi' => $tgl_presensi,
    //             'jam_in' => $jam,
    //             'foto_in' => $fileName
    //         ];

    //         $simpan = DB::table('presensi')->insert($data);
    //         if ($simpan) {
    //             Storage::put($file, $image_base64);
    //             return "success|Absen masuk berhasil";
    //         } else {
    //             return "error|Gagal absen masuk";
    //         }
    //     }
    // }

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

    public function laporan()
    {
        $namabulan = ["", "Januari", "Februari", " Maret", "April", "Mei", "Juni", "Juli", "Agustus", " September", "Oktober", "November", "Desember"];
        $karyawan = DB::table('karyawan')->orderBy('nama')->get();
        return view("presensi.laporan", compact('namabulan', 'karyawan'));
    }

    public function cetaklaporan(Request $request)
    {
        $id_karyawan = $request->id_karyawan;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
        $presensi = DB::table('presensi')->where('id_karyawan', $id_karyawan)->whereRaw('MONTH(tgl_presensi)="'.$bulan. '"')->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')->orderBy('tgl_presensi')->get();

        $pdf = Pdf::loadView('presensi.cetaklaporan', compact('namabulan', 'bulan', 'tahun', 'karyawan', 'presensi'));
        return $pdf->stream('laporan-rekap-presensi.pdf');
    }

    public function rekap()
    {
        $namabulan = ["", "Januari", "Februari", " Maret", "April", "Mei", "Juni", "Juli", "Agustus", " September", "Oktober", "November", "Desember"];

        return view("presensi.rekap", compact('namabulan'));
    }

    // public function cetakrekap(Request $request)
    // {
    //     $bulan = $request->bulan;
    //     $tahun = $request->tahun;
    //     $rekap = DB::table('presensi')
    //         ->selectRaw('presensi.id_karyawan, nama,
    //             MAX(DAY(tgl_presensi) = 1,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_1,
    //             MAX(DAY(tgl_presensi) = 2,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_2,
    //             MAX(DAY(tgl_presensi) = 3,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_3,
    //             MAX(DAY(tgl_presensi) = 4,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_4,
    //             MAX(DAY(tgl_presensi) = 5,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_5,
    //             MAX(DAY(tgl_presensi) = 6,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_6,
    //             MAX(DAY(tgl_presensi) = 7,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_7,
    //             MAX(DAY(tgl_presensi) = 8,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_8,
    //             MAX(DAY(tgl_presensi) = 9,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_9,
    //             MAX(DAY(tgl_presensi) = 10,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_10,
    //             MAX(DAY(tgl_presensi) = 11,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_11,
    //             MAX(DAY(tgl_presensi) = 12,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_12,
    //             MAX(DAY(tgl_presensi) = 13,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_13,
    //             MAX(DAY(tgl_presensi) = 14,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_14,
    //             MAX(DAY(tgl_presensi) = 15,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_15,
    //             MAX(DAY(tgl_presensi) = 16,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_16,
    //             MAX(DAY(tgl_presensi) = 17,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_17,
    //             MAX(DAY(tgl_presensi) = 18,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_18,
    //             MAX(DAY(tgl_presensi) = 19,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_19,
    //             MAX(DAY(tgl_presensi) = 20,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_20,
    //             MAX(DAY(tgl_presensi) = 21,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_21,
    //             MAX(DAY(tgl_presensi) = 22,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_22,
    //             MAX(DAY(tgl_presensi) = 23,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_23,
    //             MAX(DAY(tgl_presensi) = 24,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_24,
    //             MAX(DAY(tgl_presensi) = 25,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_25,
    //             MAX(DAY(tgl_presensi) = 26,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_26,
    //             MAX(DAY(tgl_presensi) = 27,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_27,
    //             MAX(DAY(tgl_presensi) = 28,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_28,
    //             MAX(DAY(tgl_presensi) = 29,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_29,
    //             MAX(DAY(tgl_presensi) = 30,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_30,
    //             MAX(DAY(tgl_presensi) = 31,CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"") as tgl_31')
    //         ->join('karyawan', 'presensi.id_karyawan', '=', 'karyawan.id_karyawan')
    //         ->whereRaw('MONTH(tgl_presensi)= "' . $bulan . '"')
    //         ->whereRaw('YEAR(tgl_presensi)= "' . $tahun . '"')
    //         ->groupByRaw('presensi.id_karyawan, nama')
    //         ->get();
    //     dd($rekap);
    //     // return view();
    // }

    public function cetakrekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $selectRaw = 'presensi.id_karyawan, nama';

        for ($i = 1; $i <= 31; $i++) {
            $selectRaw .= ',
                MAX(CASE WHEN DAY(tgl_presensi) = ' . $i . ' THEN CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")) ELSE "" END) as tgl_' . $i;
        }

        $rekap = DB::table('presensi')
            ->selectRaw($selectRaw)
            ->join('karyawan', 'presensi.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereMonth('tgl_presensi', $bulan)
            ->whereYear('tgl_presensi', $tahun)
            ->groupBy('presensi.id_karyawan', 'nama')
            ->get();

        // dd($rekap);
        // return view('presensi.cetakrekap', compact('rekap', 'bulan', 'tahun', 'namabulan'));
        $pdf = PDF::loadView('presensi.cetakrekap', compact('rekap', 'bulan', 'tahun', 'namabulan'))
        ->setPaper('A4', 'landscape');

        return $pdf->stream('rekap-presensi.pdf');
    }

    public function laporanrekap(Request $request)
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = Karyawan::all();
        $rekap = [];

        $hari_awal = $request->hari_awal;
        $hari_akhir = $request->hari_akhir;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        if ($tahun && $bulan) {
            $hari_awal = $hari_awal ?: 1;
            $hari_akhir = $hari_akhir ?: Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->day;
            $tanggal_awal = Carbon::createFromDate($tahun, $bulan, $hari_awal)->startOfDay();
            $tanggal_akhir = Carbon::createFromDate($tahun, $bulan, $hari_akhir)->endOfDay();
        } elseif ($tahun) {
            $tanggal_awal = Carbon::createFromDate($tahun, 1, 1)->startOfDay();
            $tanggal_akhir = Carbon::createFromDate($tahun, 12, 31)->endOfDay();
        } else {
            $tanggal_awal = Carbon::create(2025, 5, 25)->startOfDay();
            $tanggal_akhir = now()->endOfDay();
        }

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

            if (!$mulai_absen) {
                continue;
            }

            foreach ($daftarTanggal as $tanggal) {
                if ($tanggal < $mulai_absen) {
                    continue;
                }

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

        return view('presensi.laporanrekap', compact('rekap', 'namabulan'));
    }

    public function cetakPDF(Request $request)
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = Karyawan::all();
        $rekap = [];

        $hari_awal = $request->hari_awal;
        $hari_akhir = $request->hari_akhir;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        if ($tahun && $bulan) {
            $hari_awal = $hari_awal ?: 1;
            $hari_akhir = $hari_akhir ?: Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->day;
            $tanggal_awal = Carbon::createFromDate($tahun, $bulan, $hari_awal)->startOfDay();
            $tanggal_akhir = Carbon::createFromDate($tahun, $bulan, $hari_akhir)->endOfDay();
        } elseif ($tahun) {
            $tanggal_awal = Carbon::createFromDate($tahun, 1, 1)->startOfDay();
            $tanggal_akhir = Carbon::createFromDate($tahun, 12, 31)->endOfDay();
        } else {
            $tanggal_awal = Carbon::create(2025, 5, 25)->startOfDay();
            $tanggal_akhir = now()->endOfDay();
        }

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

        $pdf = \PDF::loadView('presensi.laporan_pdf', compact('rekap', 'namabulan', 'bulan', 'tahun', 'tanggal_awal', 'tanggal_akhir'));
        return $pdf->stream('laporan-rekap-presensi.pdf');
    }

}
