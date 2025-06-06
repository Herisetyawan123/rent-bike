<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Renter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
