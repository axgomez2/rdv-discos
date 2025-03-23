<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CreateCacheTablesSeeder::class,
            PlaylistSeeder::class,
            WeightSeeder::class,
            DimensionSeeder::class,
            BrandSeeder::class,
            EquipmentCategorySeeder::class,
            ProductTypeSeeder::class,
            SubscriptionPackageSeeder::class,
        ]);
    }
}
