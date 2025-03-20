<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dimension;
use App\Models\Weight;
use App\Models\VinylSec;

class UpdateShippingDimensions extends Command
{
    protected $signature = 'shipping:update-dimensions';
    protected $description = 'Update dimensions and weights for shipping calculations';

    public function handle()
    {
        $this->info('Updating dimensions for shipping...');

        // Create or update shipping package dimensions
        $lpPackage = Dimension::updateOrCreate(
            ['name' => 'LP Package'],
            [
                'height' => 35,
                'width' => 35,
                'depth' => 3,
                'unit' => 'cm'
            ]
        );

        $doubleLpPackage = Dimension::updateOrCreate(
            ['name' => '2xLP Package'],
            [
                'height' => 35,
                'width' => 35,
                'depth' => 4,
                'unit' => 'cm'
            ]
        );

        // Update weights if needed
        $singleWeight = Weight::updateOrCreate(
            ['name' => 'single'],
            [
                'value' => 280,
                'unit' => 'g'
            ]
        );

        $doubleWeight = Weight::updateOrCreate(
            ['name' => 'duplo'],
            [
                'value' => 560,
                'unit' => 'g'
            ]
        );

        // Update all vinyl records with new shipping dimensions
        $count = 0;
        VinylSec::chunk(100, function ($vinyls) use ($lpPackage, $doubleLpPackage, $singleWeight, $doubleWeight, &$count) {
            foreach ($vinyls as $vinyl) {
                // Check format to determine dimensions and weight
                if (str_contains(strtolower($vinyl->format), '2x') ||
                    str_contains(strtolower($vinyl->format), 'duplo')) {
                    $vinyl->dimension_id = $doubleLpPackage->id;
                    $vinyl->weight_id = $doubleWeight->id;
                } else {
                    $vinyl->dimension_id = $lpPackage->id;
                    $vinyl->weight_id = $singleWeight->id;
                }

                $vinyl->save();
                $count++;
            }
        });

        $this->info("Updated {$count} vinyl records with shipping dimensions.");
        $this->info('LP Package dimensions: ' . $lpPackage->width . 'x' . $lpPackage->height . 'x' . $lpPackage->depth . ' ' . $lpPackage->unit);
        $this->info('2xLP Package dimensions: ' . $doubleLpPackage->width . 'x' . $doubleLpPackage->height . 'x' . $doubleLpPackage->depth . ' ' . $doubleLpPackage->unit);
        $this->info('Single weight: ' . $singleWeight->value . ' ' . $singleWeight->unit);
        $this->info('Double weight: ' . $doubleWeight->value . ' ' . $doubleWeight->unit);
    }
}
