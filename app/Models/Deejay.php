<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Deejay extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'social_media', 'bio', 'image', 'is_active'];
    protected $table = 'deejays';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dj) {
            $dj->slug = Str::slug($dj->name);
        });

        static::updating(function ($dj) {
            if ($dj->isDirty('name')) {
                $dj->slug = Str::slug($dj->name);
            }
        });
    }
    public function recommendations()
    {
        return $this->belongsToMany(VinylMaster::class, 'deejay_vinyl_chart', 'dj_id', 'vinyl_master_id')
                    ->using(DJVinylRecommendation::class)
                    ->withPivot('order')
                    ->orderBy('order');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
