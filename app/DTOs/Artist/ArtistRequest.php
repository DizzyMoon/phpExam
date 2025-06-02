<?php

namespace App\DTOs\Artist;

class ArtistRequest {
    public int $artistId;
    public string $name;

    public function __construct(int $id, string $name) {
        $this->artistId = $id;
        $this->name = $name;
    }
}