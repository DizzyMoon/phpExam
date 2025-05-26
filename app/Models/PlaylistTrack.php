<?php


namespace App\Models;

class PlaylistTrack {
    public int $id;
    public ?Playlist $playlist;
    public ?Track $track;
}