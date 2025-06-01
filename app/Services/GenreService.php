<?php

namespace App\Services;

use App\Repositories\GenreRepository;
use PDO;

class GenreService {
    private $repository;

    public function __construct(PDO $db) {
        $this->repository = new GenreRepository($db);
    }

    public function getAllGenres() {
        return $this->repository->getAll();
    }

    public function getGenreById($id): mixed {
        return $this->repository->getById($id);
    }
}