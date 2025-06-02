<?php

namespace App\DTOs\Playlist;

class PlaylistRequest {
    public string $name;
    public function __construct (string $name) {
        $this->name = $name;
    }
}