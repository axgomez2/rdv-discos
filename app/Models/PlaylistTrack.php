<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PlaylistTrack extends Model
{
    use HasFactory;

    protected $table = 'playlist_tracks';

    protected $fillable = [
        'playlist_id',
        'vinyl_master_id',
        'trackable_type',
        'trackable_id',
        'position'
    ];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function vinylMaster()
    {
        return $this->belongsTo(VinylMaster::class);
    }

    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }
}
