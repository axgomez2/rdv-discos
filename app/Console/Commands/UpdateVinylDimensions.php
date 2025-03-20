<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VinylSec;
use App\Models\Dimension;
use App\Models\Weight;

class UpdateVinylDimensions extends Command
{
    protected $signature = 'vinyl:update-dimensions';
    protected $description = 'Update vinyl dimensions and weights based on format';

    public function handle()
    {
        $this->info('Starting vinyl dimensions update...');

        // Get or create default dimensions
        $lpDimension = Dimension::firstOrCreate(
            ['name' => 'LP'],
            [
                'height' => 33,
                'width' => 33,
                'depth' => 2,
                'unit' => 'cm'
            ]
        );

        $doubleLpDimension = Dimension::firstOrCreate(
            ['name' => '2xLP'],
            [
                'height' => 34,
                'width' => 34,
                'depth' => 3,
                'unit' => 'cm'
            ]
        );

        // Get or create default weights
        $singleWeight = Weight::firstOrCreate(
            ['name' => 'single'],
            [
                'value' => 280,
                'unit' => 'g'
            ]
        );

        $doubleWeight = Weight::firstOrCreate(
            ['name' => 'duplo'],
            [
                'value' => 560,
                'unit' => 'g'
            ]
        );

        // Update all vinyl records
        $vinylsUpdated = 0;
        VinylSec::chunk(100, function ($vinyls) use ($lpDimension, $doubleLpDimension, $singleWeight, $doubleWeight, &$vinylsUpdated) {
            foreach ($vinyls as $vinyl) {
                if (!$vinyl->dimension_id || !$vinyl->weight_id) {
                    // Check format to determine dimensions and weight
                    if (str_contains(strtolower($vinyl->format), '2x') || str_contains(strtolower($vinyl->format), 'duplo')) {
                        $vinyl->dimension_id = $doubleLpDimension->id;
                        $vinyl->weight_id = $doubleWeight->id;
                    } else {
                        $vinyl->dimension_id = $lpDimension->id;
                        $vinyl->weight_id = $singleWeight->id;
                    }
                    $vinyl->save();
                    $vinylsUpdated++;
                }
            }
        });

        $this->info("Updated {$vinylsUpdated} vinyl records with dimensions and weights.");
    }
}
