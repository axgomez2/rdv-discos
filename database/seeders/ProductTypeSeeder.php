<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductType;
use Illuminate\Support\Str;

class ProductTypeSeeder extends Seeder
{
    public function run()
    {
        $types = ['Vinyl', 'Equipment'];

        foreach ($types as $type) {
            ProductType::firstOrCreate([
                'name' => $type,
                'slug' => Str::slug($type)
            ]);
        }
    }
}
