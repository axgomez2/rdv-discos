<?php

namespace Database\Seeders;

use App\Models\SubscriptionPackage;
use Illuminate\Database\Seeder;

class SubscriptionPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Plano Básico',
                'category' => 'Iniciante',
                'description' => 'Perfeito para quem está começando sua coleção de vinis. Receba 1 vinil cuidadosamente selecionado por mês.',
                'price' => 99.90,
                'vinyl_quantity' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Plano Premium',
                'category' => 'Colecionador',
                'description' => 'Para os verdadeiros amantes de música. Receba 2 vinis premium por mês, incluindo edições especiais e raridades.',
                'price' => 189.90,
                'vinyl_quantity' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Plano Colecionador',
                'category' => 'Especialista',
                'description' => 'A experiência definitiva para colecionadores. Receba 3 vinis por mês, incluindo edições limitadas e imports.',
                'price' => 279.90,
                'vinyl_quantity' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            SubscriptionPackage::create($package);
        }
    }
}
