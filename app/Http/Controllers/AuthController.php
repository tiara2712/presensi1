<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function proseslogin(Request $request)
    {
        // $pass = 1234;
        // echo Hash::make($pass);

        // dd($request->only('id_karyawan', 'password'));
        if (Auth::guard('karyawan')->attempt(['id_karyawan' => $request->id_karyawan, 'password' => $request->password])) {
            return redirect ('/dashboard');
        } else {
            return redirect('/')->with(['warning' => 'Id Karyawan / Password Salah']);
        }
    }

    public function proseslogout()
    {
        if(Auth::guard('karyawan')->check()) {
            Auth::guard('karyawan')->logout();
            return redirect('/');
        }
    }

    public function prosesloginadmin(Request $request)
    {
        // $pass = 'admin';
        // echo Hash::make($pass);

        // dd($request->only('email', 'password'));

        if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect ('/admin/dashboardadmin');
        } else {
            return redirect('/admin')->with(['warning' => 'Username / Password Salah']);
        }
    }

    public function proseslogoutadmin()
    {
        if(Auth::guard('user')->check()) {
            Auth::guard('user')->logout();
            return redirect('/admin');
        }
    }

}
