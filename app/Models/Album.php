<?php

namespace App\Models;

class Album {
    public int $albumId;
    public string $title;
    public ?Artist $artist;

    public function __construct(int $albumId, string $title, ?Artist $artist) {
        $this->albumId = $albumId;
        $this->title = $title;
        $this->artist = $artist;
    }
}