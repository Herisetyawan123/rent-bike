<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
        $renterRole = Role::firstOrCreate(['name' => 'renter']);

        // Buat admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // ganti password di production
            ]
        );
        $admin->assignRole($adminRole);

        // Buat vendor
        $vendor = User::firstOrCreate(
            ['email' => 'vendor@example.com'],
            [
                'name' => 'Vendor Motor',
                'password' => Hash::make('password'),
            ]
        );

        Vendor::create([
            'user_id' => $vendor->id,
            'business_name' => 'Software Host',
            'contact_person_name' => 'Heri Setyawan',
            'business_address' => 'Jauh sana sekali',
            'phone' => '652999999',
            'national_id' => '097234987987'
        ]);
        $vendor->assignRole($vendorRole);

        $this->command->info('Admin dan Vendor berhasil dibuat.');
    }
}
