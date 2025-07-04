<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
            // dd();
            // dd(Auth::user()->hasRole('admin'));
            if(Auth::user()->hasRole('admin')) {
                return redirect('/admin');
            }

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

    public function logout(Request $request)
    {
        Auth::logout(); // Logout user

        $request->session()->invalidate(); // Hapus session
        $request->session()->regenerateToken(); // Regenerasi CSRF token

        return redirect('/login'); // Redirect ke halaman login
    }

   /**
     * Tampilkan form edit profile.
     */
    public function edit()
    {
        $user   = Auth::user();          // users table
        $vendor = $user->vendor;         // relasi hasOne (Vendor)
        $areas = \App\Models\Area::all();
        return view('pages.profile.edit', compact('user', 'vendor', 'areas'));
    }

    /**
     * Proses update profile (users & vendors).
     */
    public function update(Request $request)
    {
        $user   = Auth::user();
        $vendor = $user->vendor;         // bisa null jika user bukan vendor

        // ---------- VALIDASI ----------
        $request->validate([
            // users
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'  => ['required', 'string', 'max:30', Rule::unique('users')->ignore($user->id)],
            'photo'  => ['nullable', 'image', 'max:2048'],

            // vendor (pakai sometimes supaya aman bila bukan vendor)
            'business_name'        => ['sometimes', 'required', 'string', 'max:255'],
            'contact_person_name'  => ['sometimes', 'required', 'string', 'max:255'],
            'tax_id'               => ['nullable', 'string', 'max:50'],
            'business_address'     => ['sometimes', 'required', 'string', 'max:500'],
            'latitude'             => ['nullable', 'numeric'],
            'longitude'            => ['nullable', 'numeric'],
            'photo_attachment'     => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'national_id'          => ['sometimes', 'required', 'string', 'max:50'],
            'legal_documents'      => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ]);

        // ---------- UPDATE ----------
        DB::transaction(function () use ($request, $user, $vendor) {

            // users table
            $user->fill($request->only(['name', 'email', 'phone']));

            if ($request->hasFile('photo')) {
                // hapus foto lama jika bukan default
                if ($user->photo && $user->photo !== '/img/default.png') {
                    Storage::delete($user->photo);
                }
                $user->photo = $request->file('photo')->store('photos/users', 'public');
            }
            $user->save();

            // vendors table (jika ada)
            if ($vendor) {
                $vendor->fill($request->only([
                    'business_name',
                    'contact_person_name',
                    'tax_id',
                    'business_address',
                    'latitude',
                    'longitude',
                    'national_id'
                ]));

                if ($request->hasFile('photo_attachment')) {
                    if ($vendor->photo_attachment) {
                        Storage::delete($vendor->photo_attachment);
                    }
                    $vendor->photo_attachment = $request->file('photo_attachment')->store('photos/vendors', 'public');
                }

                // dd($request->file('legal_documents'));
                if ($request->hasFile('legal_documents')) {
                    \Log::info('File legal_documents ditemukan', [
                        'original_name' => $request->file('legal_documents')->getClientOriginalName(),
                        'size' => $request->file('legal_documents')->getSize()
                    ]);
                    if ($vendor->legal_documents) {
                        Storage::delete($vendor->legal_documents);
                    }
                    $vendor->legal_documents = $request->file('legal_documents')->store('docs/vendors', 'public');
                }

                $vendor->save();
            }
        });

        return back()->with('success', 'Profile berhasil diperbarui!');
    }


}
