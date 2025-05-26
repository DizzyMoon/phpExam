<?php

namespace App\DTOs\Track;

class TrackResponse {
    public int $trackId;
    public string $name;
    public string $albumName;
    public string $mediaTypeName;
    public string $genreName;
    public string $composer;
    public int $milliseconds;
    public float $bytes;
    public float $unitPrice;

    public function __construct($trackId, $name, $albumName, $mediaTypeName, $genreName, $composer, $milliseconds, $bytes, $unitPrice){
        $this->trackId = $trackId;
        $this->name = $name;
        $this->albumName = $albumName;
        $this->mediaTypeName = $mediaTypeName;
        $this->genreName = $genreName;
        $this->composer = $composer;
        $this->milliseconds = $milliseconds;
        $this->bytes = $bytes;
        $this->unitPrice = $unitPrice;
    }
}