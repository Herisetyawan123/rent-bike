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
                ],
                [
                    'name' => 'Sarung Tangan',
                ],
                [
                    'name' => 'Jaket',
                ],
            ]);
    }
}
