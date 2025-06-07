<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.custom-login'); // view form login custom nanti dibuat
    }

    public function login(Request $request)
    {
        // validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // coba login
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // redirect ke dashboard atau halaman lain
            return redirect()->intended('/admin');
        }

        // kalau gagal login, balik lagi dengan error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
