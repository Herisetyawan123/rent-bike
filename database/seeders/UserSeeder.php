<?php

namespace Database\Seeders;

use App\Constant\VendorPermission;
use App\Models\Area;
use App\Models\Renter;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission as SpatiePermission;
use ReflectionClass;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = (new ReflectionClass(VendorPermission::class))->getConstants();

        foreach ($permissions as $perm) {
            SpatiePermission::firstOrCreate(['name' => $perm]);
        }

        // Cari role 'vendor'
        $vendorRole = Role::firstOrCreate(['name' => 'vendor']);

        // Assign semua permission ke role vendor
        $vendorRole->givePermissionTo(array_values($permissions));
        
        // Buat role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $renterRole = Role::firstOrCreate(['name' => 'renter']);


        // Buat admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Super Admin',
                'phone' => '6283853797951',
                'password' => Hash::make('password'), // ganti password di production
            ]
        );
        $admin->assignRole($adminRole);

        // Buat vendor
        $vendor = User::firstOrCreate(
            ['email' => 'vendor@mail.com'],
            [
                'name' => 'Vendor Motor',
                'password' => Hash::make('password'),
                'phone' => '6283853797952',
            ]
        );

        $area = Area::insert([
            [
                'name' => 'Bali Denpasar',
            ],
            [
                'name' => 'Bali Badung',
            ],
            [
                'name' => 'Bali Gianyar',
            ],
        ]);

        Vendor::create([
            'user_id' => $vendor->id,
            'business_name' => 'Software Host',
            'contact_person_name' => 'Heri Setyawan',
            'business_address' => 'Jauh sana sekali',
            'national_id' => '097234987987',
            'area_id' => Area::where('name', 'Bali Denpasar')->first()->id, // Asumsi area sudah ada
        ]);
        $vendor->assignRole($vendorRole);


        $user = User::create([
            'name' => 'Rina Putri',
            'email' => 'renter@mail.com',
            'password' => Hash::make('password'),
            'phone' => '6283853797950',
        ]);

        // Assign role kalau pakai Spatie
        $user->assignRole('renter');

        // Buat data renter
        Renter::create([
            'user_id' => $user->id,
            'national_id' => '1234567890123456',
            'driver_license_number' => 'B1234XYZ',
            'gender' => 'female',
            'ethnicity' => 'Javanese',
            'nationality' => 'Indonesia',
            'birth_date' => '1995-04-10',
            'address' => 'Jalan Merdeka No. 10, Surabaya',
            'current_address' => 'Kost Putri Indah, Surabaya',
            'marital_status' => 'single',
        ]);
        $this->command->info('Admin dan Vendor berhasil dibuat.');
    }
}
