<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DJVinylRecommendation extends Pivot
{
    protected $table = 'dj_vinyl_recommendations';

    public $incrementing = false;

    protected $fillable = ['dj_id', 'vinyl_master_id', 'order'];
}
