<?php


namespace App\Services;

use App\Repositories\ArtistRepository;
use PDO;

class ArtistService {
    private $repository;

    public function __construct(PDO $db) {
        $this->repository = new ArtistRepository($db);
    }

    public function getArtists() {
        return $this->repository->getAll();
    }

    public function getArtistById($id) {
        return $this->repository->getById($id);
    }
}