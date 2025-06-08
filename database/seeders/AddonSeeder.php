<?php

namespace Database\Seeders;

use App\Models\AddOn;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AddOn::insert([
                [
                    'name' => 'Helm',
                    'price' => 1000,
                ],
                [
                    'name' => 'Sarung Tangan',
                    'price' => 1000,
                ],
                [
                    'name' => 'Jaket',
                    'price' => 1000,
                ],
            ]);
    }
}
