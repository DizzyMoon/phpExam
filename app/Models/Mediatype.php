<?php

namespace App\Models;

class MediaType {
    public int $mediaTypeId;
    public string $name;

    public function __construct(int $mediaTypeId, string $name){
        $this->mediaTypeId = $mediaTypeId;
        $this->name = $name;
    }
}