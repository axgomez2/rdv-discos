<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasWishlist;

class VinylMaster extends Model
{
    use HasFactory, SoftDeletes, HasWishlist;

    protected $fillable = [
        'title',
        'slug',
        'discogs_id',
        'description',
        'cover_image',
        'images',
        'discogs_url',
        'release_year',
        'country',
        'record_label_id',
        'is_published'
    ];

    protected $casts = [
        'images' => 'array',
        'release_year' => 'integer',
        'is_published' => 'boolean',
    ];

    protected $with = ['artists', 'tracks', 'vinylSec', 'recordLabel'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vinylMaster) {
            $vinylMaster->slug = Str::slug($vinylMaster->title);
        });
    }

    public function recordLabel()
    {
        return $this->belongsTo(RecordLabel::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function styles()
    {
        return $this->belongsToMany(Style::class);
    }

    public function artists()
    {
        return $this->belongsToMany(Artist::class);
    }

    public function vinylSec(): HasOne
    {
        return $this->hasOne(VinylSec::class);
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function product()
    {
        return $this->morphOne(Product::class, 'productable');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function saveExternalImage($url)
    {
        $contents = file_get_contents($url);
        $name = 'vinyl_covers/' . Str::random(40) . '.jpg';
        Storage::disk('public')->put($name, $contents);
        $this->cover_image = $name;
        $this->save();
    }

    public function inWishlist(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->wishlist()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function inWantlist(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->wantlist()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function wishlist()
    {
        return $this->morphMany(Wishlist::class, 'product');
    }

    public function wantlist()
    {
        return $this->morphMany(Wantlist::class, 'product');
    }

    public function wantlists()
    {
        return $this->morphMany(Wantlist::class, 'product');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id');
    }

    public function catStyleShops()
    {
        return $this->belongsToMany(CatStyleShop::class, 'cat_style_shop_vinyl_master', 'vinyl_master_id', 'cat_style_shop_id');
    }

    public function playlistTracks()
    {
        return $this->hasMany(PlaylistTrack::class);
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_tracks')
                    ->withPivot(['position', 'trackable_type', 'trackable_id'])
                    ->orderBy('playlist_tracks.position')
                    ->withTimestamps();
    }
}
