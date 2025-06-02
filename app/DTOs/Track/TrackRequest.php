<?php

namespace App\DTOs\Track;
use App\DTOs\Request;

class TrackRequest implements Request
{
    public string $name;
    public int $albumId;
    public int $mediaTypeId;
    public int $genreId;
    public string $composer;
    public int $milliseconds;
    public float $bytes;
    public float $unitPrice;

    public function __construct(
        string $name,
        int $albumId,
        int $mediaTypeId,
        int $genreId,
        string $composer,
        int $milliseconds,
        float $bytes,
        float $unitPrice
    ) {
        $this->name = $name;
        $this->albumId = $albumId;
        $this->mediaTypeId = $mediaTypeId;
        $this->genreId = $genreId;
        $this->composer = $composer;
        $this->milliseconds = $milliseconds;
        $this->bytes = $bytes;
        $this->unitPrice = $unitPrice;
    }
}