<?php

namespace App\Models;

class Artist {
    public int $artistId;
    public string $name;

    public function __construct(int $artistId, string $name) {
        $this->artistId = $artistId;
        $this->name = $name;
    }
}