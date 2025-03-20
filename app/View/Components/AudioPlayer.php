<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AudioPlayer extends Component
{
    public $tracks;
    public $title;
    public $artist;
    public $cover;

    public function __construct($tracks = [], $title = null, $artist = null, $cover = null)
    {
        $this->tracks = $tracks;
        $this->title = $title;
        $this->artist = $artist;
        $this->cover = $cover;
    }

    public function render()
    {
        return view('components.audio-player');
    }
}

