<?php


namespace App\Models;

class PlaylistTrack {
    public int $id;
    public ?Playlist $playlist;
    public ?Track $track;

    public function __construct(int $id, Playlist $playlist, Track $track) {
        $this->id = $id;
        $this->playlist = $playlist;
        $this->track = $track;
    }
}