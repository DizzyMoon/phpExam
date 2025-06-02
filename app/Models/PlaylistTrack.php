<?php


namespace App\Models;

class PlaylistTrack
{
    public ?Playlist $playlist;
    public ?Track $track;

    public function __construct(Playlist $playlist, Track $track)
    {
        $this->playlist = $playlist;
        $this->track = $track;
    }
}