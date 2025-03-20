<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'bio',
        'instagram_url',
        'youtube_url',
        'facebook_url',
        'soundcloud_url',
        'image',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url', 'status_texto'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($playlist) {
            if (empty($playlist->slug)) {
                $playlist->slug = Str::slug($playlist->name);
            }
        });
    }

    public function tracks()
    {
        return $this->hasMany(PlaylistTrack::class)->orderBy('position');
    }

    public function discos()
    {
        return $this->belongsToMany(VinylMaster::class, 'playlist_tracks')
                    ->withPivot(['position', 'trackable_type', 'trackable_id'])
                    ->orderBy('playlist_tracks.position')
                    ->withTimestamps();
    }

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/default-playlist.svg');
    }

    public function getStatusTextoAttribute()
    {
        return $this->is_active ? 'Ativa' : 'Inativa';
    }

    public function addTrack(VinylMaster $vinylMaster, $trackable)
    {
        // Verifica se a playlist já tem 10 faixas
        if ($this->tracks()->count() >= 10) {
            return false;
        }

        // Obtém a próxima posição
        $position = $this->tracks()->max('position') + 1;

        // Adiciona a faixa com relação polimórfica
        $this->tracks()->create([
            'vinyl_master_id' => $vinylMaster->id,
            'trackable_type' => get_class($trackable),
            'trackable_id' => $trackable->id,
            'position' => $position
        ]);

        return true;
    }

    public function removeTrack($trackId)
    {
        $track = $this->tracks()->find($trackId);
        if ($track) {
            $track->delete();

            // Reordena as faixas restantes
            $this->tracks()
                ->where('position', '>', $track->position)
                ->update(['position' => \DB::raw('position - 1')]);
        }
    }

    public function reorderTrack($trackId, int $newPosition)
    {
        $track = $this->tracks()->find($trackId);
        if (!$track) return;

        $currentPosition = $track->position;

        if ($currentPosition === $newPosition) {
            return;
        }

        // Atualiza as posições das outras faixas
        if ($currentPosition < $newPosition) {
            $this->tracks()
                ->where('position', '>', $currentPosition)
                ->where('position', '<=', $newPosition)
                ->update(['position' => \DB::raw('position - 1')]);
        } else {
            $this->tracks()
                ->where('position', '>=', $newPosition)
                ->where('position', '<', $currentPosition)
                ->update(['position' => \DB::raw('position + 1')]);
        }

        // Define a nova posição para a faixa
        $track->update(['position' => $newPosition]);
    }

    public function hasSocialMedia(): bool
    {
        return $this->instagram_url || $this->youtube_url || 
               $this->facebook_url || $this->soundcloud_url;
    }

    public function trackCount(): int
    {
        return $this->tracks()->count();
    }
}
