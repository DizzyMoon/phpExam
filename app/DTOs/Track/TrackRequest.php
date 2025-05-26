<?php

namespace App\DTOs\Track;

class TrackRequest {
    public string $name;
    public int $albumId;
    public int $mediaTypeId;
    public int $genreId;
    public string $composer;
    public int $milliseconds;
    public float $bytes;
    public float $unitPrice;
}