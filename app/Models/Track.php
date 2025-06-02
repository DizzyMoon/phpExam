<?php

namespace App\Models;

use App\Models\Album;
use App\Models\MediaType;
use App\Models\Genre;

class Track
{
    public int $trackId;
    public string $name;
    public ?Album $album;
    public MediaType $mediaType;
    public ?Genre $genre;
    public ?string $composer;
    public int $milliseconds;
    public float $bytes;
    public float $unitPrice;


    public function __construct(
        int $trackId,
        string $name,
        ?Album $album,
        ?MediaType $mediaType,
        ?Genre $genre,
        ?string $composer,
        int $milliseconds,
        float $bytes,
        float $unitPrice,
    ) {
        $this->trackId = $trackId;
        $this->name = $name;
        $this->album = $album;
        $this->mediaType = $mediaType;
        $this->genre = $genre;
        $this->composer = $composer;
        $this->milliseconds = $milliseconds;
        $this->bytes = $bytes;
        $this->unitPrice = $unitPrice;
    }
}