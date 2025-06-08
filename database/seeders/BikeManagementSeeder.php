<?php

namespace Database\Seeders;

use App\Models\BikeCapacity;
use App\Models\BikeColor;
use App\Models\BikeMerk;
use App\Models\BikeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BikeManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $color = [
            [
                'color' => 'red',
                'color_code' => '#9e1212',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'color' => 'blue',
                'color_code' => '#6314e3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'color' => 'black',
                'color_code' => '#090312',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        BikeColor::insert($color);

        $merk = BikeMerk::create([
            'name' => 'Honda'
        ]);

        $bike_type = [
               [
                'bike_merk_id' => $merk->id,
                'name' => 'Beat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
               [
                'bike_merk_id' => $merk->id,
                'name' => 'Vario',
                'created_at' => now(),
                'updated_at' => now(),
            ],
               [
                'bike_merk_id' => $merk->id,
                'name' => 'NMAX',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        BikeType::insert($bike_type);

        BikeCapacity::insert([
                [
                    'capacity' => 50,
                    'description' => 'Motor Kecil',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'capacity' => 100,
                    'description' => 'Motor Sedang',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'capacity' => 150,
                    'description' => 'Motor Besar',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
    }
}
