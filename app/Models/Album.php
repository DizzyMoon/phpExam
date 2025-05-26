<?php

namespace App\Models;


class Album {
    public int $albumId;
    public string $title;
    public ?Artist $artist;
}