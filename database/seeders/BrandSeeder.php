<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run()
    {
        $brands = [
            [
                'name' => 'Audio-Technica',
                'description' => 'Japanese company that designs and manufactures professional microphones, headphones, phonographic magnetic cartridges, and other audio equipment.',
                'logo_url' => 'https://example.com/audio-technica-logo.png'
            ],
            [
                'name' => 'Sony',
                'description' => 'Japanese multinational conglomerate corporation headquartered in Kōnan, Minato, Tokyo.',
                'logo_url' => 'https://example.com/sony-logo.png'
            ],
            [
                'name' => 'Pioneer',
                'description' => 'Japanese multinational corporation that specializes in digital entertainment products.',
                'logo_url' => 'https://example.com/pioneer-logo.png'
            ],
            [
                'name' => 'Technics',
                'description' => 'Japanese brand name of the Panasonic Corporation for audio equipment.',
                'logo_url' => 'https://example.com/technics-logo.png'
            ],
            [
                'name' => 'Rega',
                'description' => 'British audio equipment manufacturer that specializes in high-quality turntables.',
                'logo_url' => 'https://example.com/rega-logo.png'
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand['name'],
                'slug' => Str::slug($brand['name']),
                'description' => $brand['description'],
                'logo_url' => $brand['logo_url']
            ]);
        }
    }
}
