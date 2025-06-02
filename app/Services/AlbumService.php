<?php

namespace App\Services;
use PDO;

require_once __DIR__ . "/../Models/Album.php";

use \App\Repositories\AlbumRepository;
use \App\Models\Album;
use \App\Config\Database;
use \App\DTOs\Album\AlbumRequest;

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

    public function create(string $title, int $artistId) {
        $request = new AlbumRequest($title, $artistId);
        return $this->repository->create($request);
    }

    public function update(int $albumId, string $title, int $artistId) {
        $request = new AlbumRequest($title, $artistId);
        return $this->repository->update($albumId, $request);
    }

    public function delete(int $albumId) {
        return $this->repository->delete($albumId);
    }
}