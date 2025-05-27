<?php

namespace App\Models;

class Genre {
    public int $genreId;
    public string $name;


    public function __construct (int $genreId, string $name){
        $this->genreId = $genreId;
        $this->name = $name;
    }
}