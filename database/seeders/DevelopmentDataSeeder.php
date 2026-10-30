<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\VillaType;
use App\Models\VillaUnit;
use Illuminate\Database\Seeder;

class DevelopmentDataSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::updateOrCreate(
            ['slug' => 'villa-demo-bali'],
            [
                'name' => 'Villa Demo Bali',
                'description' => 'Cabang untuk pengujian sistem.',
                'address' => 'Jl. Contoh No. 1',
                'city' => 'Badung',
                'province' => 'Bali',
                'phone' => '080000000000',
                'is_active' => true,
            ]
        );

        $villaType = VillaType::updateOrCreate(
            [
                'branch_id' => $branch->id,
                'slug' => 'one-bedroom-demo',
            ],
            [
                'name' => 'One Bedroom Demo',
                'description' => 'Tipe villa untuk pengujian.',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'standard_capacity' => 2,
                'max_capacity' => 3,
                'is_active' => true,
            ]
        );

        VillaUnit::updateOrCreate(
            [
                'villa_type_id' => $villaType->id,
                'unit_code' => 'DEMO-01',
            ],
            [
                'unit_name' => 'Unit Demo 01',
                'condition_status' => 'good',
                'notes' => 'Unit khusus data pengujian.',
            ]
        );
    }
}
