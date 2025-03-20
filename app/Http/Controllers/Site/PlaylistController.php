<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function index()
    {
        $playlists = Playlist::with(['tracks.vinylMaster', 'tracks.trackable'])
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        return view('site.playlists.index', compact('playlists'));
    }

    public function show($slug)
    {
        $playlist = Playlist::with(['tracks.vinylMaster', 'tracks.trackable'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('site.playlists.show', compact('playlist'));
    }

    public function getPlaylistTracks($slug)
    {
        $playlist = Playlist::with(['tracks.vinylMaster', 'tracks.trackable'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $tracks = $playlist->tracks->map(function ($track) {
            $vinylMaster = $track->vinylMaster;
            $trackable = $track->trackable;

            return [
                'id' => $track->id,
                'position' => $track->position,
                'title' => $vinylMaster->title,
                'artist' => $vinylMaster->artists->pluck('name')->implode(', '),
                'cover' => $vinylMaster->cover_url,
                'preview_url' => $trackable->preview_url ?? null,
                'duration' => $trackable->duration ?? null,
            ];
        });

        return response()->json($tracks);
    }
}
