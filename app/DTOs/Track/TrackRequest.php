<?php

namespace App\DTOs\Track;
use App\DTOs\Request;

class TrackRequest implements Request {
    public string $name;
    public int $albumId;
    public int $mediaTypeId;
    public int $genreId;
    public string $composer;
    public int $milliseconds;
    public float $bytes;
    public float $unitPrice;
}