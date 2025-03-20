<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Weight;

class WeightSeeder extends Seeder
{
    public function run()
    {
        $weights = [
            ['name' => 'single', 'value' => 280, 'unit' => 'g'],
            ['name' => 'duplo', 'value' => 560, 'unit' => 'g'],
            ['name' => 'triplo', 'value' => 800, 'unit' => 'g'],
            ['name' => 'Audiophile', 'value' => 180, 'unit' => 'g'],
            ['name' => 'Super Audiophile', 'value' => 200, 'unit' => 'g'],
        ];

        foreach ($weights as $weight) {
            Weight::create($weight);
        }
    }
}
