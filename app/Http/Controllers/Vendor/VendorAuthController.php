<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VendorAuthController extends Controller
{
 public function showLoginForm()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('admin-vendor.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function showRegisterForm()
    {
        return view('pages.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|unique:users,email',
            'phone'                => 'required|string|unique:users,phone',
            'password'             => 'required|confirmed|min:6',
            'business_name'        => 'required|string',
            'contact_person_name'  => 'required|string',
            'business_address'     => 'required|string',
            'national_id'          => 'required|string',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        Vendor::create([
            'user_id'              => $user->id,
            'business_name'        => $request->business_name,
            'contact_person_name'  => $request->contact_person_name,
            'business_address'     => $request->business_address,
            'national_id'          => $request->national_id,
        ]);

        Auth::login($user);

        return redirect()->route('admin-vendor.dashboard');
    }
}
