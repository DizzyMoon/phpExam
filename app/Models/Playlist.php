<?php

namespace App\Models;


class Playlist {
    public int $playlistId;
    public string $name;

    public function __construct(int $playlistId, string $name){
        $this->playlistId = $playlistId;
        $this->name = $name;
    }
}