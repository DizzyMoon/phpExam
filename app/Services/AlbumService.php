<?php

namespace App\Services;
use PDO;

require_once __DIR__ . "/../Models/Album.php";

use \App\Repositories\AlbumRepository;
use \App\Models\Album;
use \App\Config\Database;

class AlbumService {
    private $repository;


    public function __construct(PDO $db) {
        $this->repository = new AlbumRepository($db);
    }

    public function getAlbums() {
        return $this->repository->getAll();
    }

    public function getAlbumById($id) {
        return $this->repository->getById($id);
    }

    public function getAlbumsByArtistId(int $artistId) {
        return $this->repository->getAlbumsByArtistId($artistId);
    }

    public function search($searchString) {
        return $this->repository->search($searchString);
    }
}