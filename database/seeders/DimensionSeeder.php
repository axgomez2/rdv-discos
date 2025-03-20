<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dimension;

class DimensionSeeder extends Seeder
{
    public function run()
    {
        $dimensions = [
            ['name' => '7"', 'height' => 18, 'width' => 18, 'depth' => 2, 'unit' => 'cm'],
            ['name' => '10"', 'height' => 25, 'width' => 25, 'depth' => 2, 'unit' => 'cm'],
            ['name' => '12"', 'height' => 33, 'width' => 33, 'depth' => 2, 'unit' => 'cm'],
            ['name' => 'LP', 'height' => 33, 'width' => 33, 'depth' => 2, 'unit' => 'cm'],
            ['name' => '2xLP', 'height' => 34, 'width' => 34, 'depth' => 3, 'unit' => 'cm'],
        ];

        foreach ($dimensions as $dimension) {
            Dimension::create($dimension);
        }
    }
}
