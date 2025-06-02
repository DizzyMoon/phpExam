<?php

namespace App\DTOs\Album;


class AlbumRequest {
    public string $title;
    public int $artistId;

    public function __construct(string $title, int $artistId) {
        $this->title = $title;
        $this->artistId = $artistId;
    }
}