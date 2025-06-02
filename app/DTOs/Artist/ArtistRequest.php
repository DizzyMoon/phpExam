<?php

namespace App\DTOs\Artist;

class ArtistRequest {
    public string $name;

    public function __construct(string $name) {
        $this->name = $name;
    }
}