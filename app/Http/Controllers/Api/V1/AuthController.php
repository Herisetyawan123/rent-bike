<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Renter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
        ]);

        $phone = preg_replace('/^0/', '62', $request->phone); // Format 62
        $otp = rand(100000, 999999);

        $user = User::where('phone', $phone)->first();

        // Cari user atau buat baru
        $user = User::updateOrCreate(
            ['phone' => $phone],
            [
                'name' => $user != null ? $user->name : 'Guest_' . $phone, 
                'password' => bcrypt(Str::random(10)), // Random password, jarang dipakai
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5),
            ]
        );

        if(!$user->renter) {
            $user->renter()->create([]);
        }
        // Kirim OTP pakai Fonnte
        $message = "Kode OTP kamu: *$otp* (berlaku 5 menit)";
        Http::withHeaders([
            'Authorization' => config('services.fonnte.key'),
        ])->post('https://api.fonnte.com/send', [
            'target' => $phone,
            'message' => $message,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'OTP berhasil dikirim',
            'user_id' => $user->id, // boleh kasih ini buat tracking
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        if (
            !$user->otp ||
            $user->otp !== $request->otp ||
            now()->gt($user->otp_expires_at)
        ) {
            return response()->json([
                'message' => 'OTP tidak valid atau sudah kedaluwarsa',
            ], 401);
        }

        // Reset OTP setelah berhasil login
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->load('renter');

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'photo' => $user->photo,
                'is_requested' => $user->is_requested,
                'renter' => [
                    'national_id' => $user->renter->national_id ?? null,
                    'driver_license_number' => $user->renter->driver_license_number ?? null,
                    // 'gender' => $user->renter->gender ?? null,
                    // 'ethnicity' => $user->renter->ethnicity ?? null,
                    // 'nationality' => $user->renter->nationality ?? null,
                    'birth_date' => $user->renter->birth_date ?? null,
                    'address' => $user->renter->address ?? null,
                    'current_address' => $user->renter->current_address ?? null,
                    // 'marital_status' => $user->renter->marital_status ?? null,
                ],
            ]
        ]);
    }

    public function checkEligibility(Request $request)
    {
        $user = $request->user()->load('renter');
        $eligibility = $user->checkEligibility();

        return response()->json([
            'status' => $eligibility['is_eligible'] && $user->is_requested,
            'message' => $eligibility['is_eligible']
                ? ($user->is_requested ? 'Masih menunggu persetujuan admin' : 'Data sudah lengkap.')
                : 'Ada data yang belum lengkap.',
            'data' => $eligibility,
        ]);
    }

    public function register(Request $request)
    {
        // Validasi input (sesuaikan dengan kebutuhan, renters mostly nullable jadi gak wajib)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',  // harus ada password_confirmation di request
            // Renter data, nullable semua jadi optional
            'national_id' => 'nullable|string|max:50',
            'driver_license_number' => 'nullable|string|max:50',
            'gender' => 'nullable|in:male,female,other',
            'ethnicity' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'current_address' => 'nullable|string',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'phone' => 'nullable|string|max:20',
        ]);

        // Gunakan transaksi agar kalau gagal rollback semua
        DB::beginTransaction();
        try {
            // Buat user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Buat renter terkait
            $renterData = [
                'user_id' => $user->id,
                'national_id' => $validated['national_id'] ?? null,
                'driver_license_number' => $validated['driver_license_number'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'ethnicity' => $validated['ethnicity'] ?? null,
                'nationality' => $validated['nationality'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'address' => $validated['address'] ?? null,
                'current_address' => $validated['current_address'] ?? null,
                'marital_status' => $validated['marital_status'] ?? null,
                'phone' => $validated['phone'] ?? null,
            ];

            Renter::create($renterData);

            DB::commit();

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // Get user profile
    public function profile(Request $request)
    {
        $user = $request->user()->load('renter');

        return response()->json([
            'message' => 'User profile fetched successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'photo' => $user->photo,
                'is_requested' => $user->is_requested,
                'renter' => [
                    'national_id' => $user->renter->national_id ?? null,
                    'driver_license_number' => $user->renter->driver_license_number ?? null,
                    'birth_date' => $user->renter->birth_date ?? null,
                    'address' => $user->renter->address ?? null,
                    'current_address' => $user->renter->current_address ?? null,
                ],
                'documents' => [
                    'national_id_front' => $user->getFirstMediaUrl('national_id_front'),
                    'national_id_back' => $user->getFirstMediaUrl('national_id_back'),
                    'driving_license_front' => $user->getFirstMediaUrl('driving_license_front'),
                    'driving_license_back' => $user->getFirstMediaUrl('driving_license_back'),
                    'selfie_with_id' => $user->getFirstMediaUrl('selfie_with_id'),
                ],
            ]
        ]);
    }

    // update user profile
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,

            // Renter fields
            // 'national_id' => 'nullable|string|max:100',
            'driver_license_number' => 'nullable|string|max:100',
            // 'gender' => 'nullable|in:male,female,other',
            // 'ethnicity' => 'nullable|string|max:100',
            // 'nationality' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'current_address' => 'nullable|string',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update user table
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Update renter table (relasi hasOne)
        $user->renter()->updateOrCreate([], [
            'national_id' => $request->national_id,
            'driver_license_number' => $request->driver_license_number,
            // 'gender' => $request->gender,
            // 'ethnicity' => $request->ethnicity,
            // 'nationality' => $request->nationality,
            'birth_date' => $request->birth_date,
            'address' => $request->address,
            'current_address' => $request->current_address,
            // 'marital_status' => $request->marital_status,
        ]);

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'user' => $user->load('renter'),
        ]);
    }

    // upload national ID
    public function uploadNationalId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'front' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'back'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        $user->addMedia($request->file('front'))
            ->usingFileName('national_id_front_' . $user->id . '.' . $request->file('front')->getClientOriginalExtension())
            ->toMediaCollection('national_id_front');

        $user->addMedia($request->file('back'))
            ->usingFileName('national_id_back_' . $user->id . '.' . $request->file('back')->getClientOriginalExtension())
            ->toMediaCollection('national_id_back');

        return response()->json(['message' => 'National ID uploaded successfully.']);
    }

    public function uploadDrivingLicense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'front' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'back'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        $user->addMedia($request->file('front'))
            ->usingFileName('driving_license_front_' . $user->id . '.' . $request->file('front')->getClientOriginalExtension())
            ->toMediaCollection('driving_license_front');

        $user->addMedia($request->file('back'))
            ->usingFileName('driving_license_back_' . $user->id . '.' . $request->file('back')->getClientOriginalExtension())
            ->toMediaCollection('driving_license_back');

        return response()->json(['message' => 'Driving license uploaded successfully.']);
    }

    public function uploadSelfieWithId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'selfie' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        $user->addMedia($request->file('selfie'))
            ->usingFileName('selfie_with_id_' . $user->id . '.' . $request->file('selfie')->getClientOriginalExtension())
            ->toMediaCollection('selfie_with_id');

        return response()->json(['message' => 'Selfie uploaded successfully.']);
    }

    
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
