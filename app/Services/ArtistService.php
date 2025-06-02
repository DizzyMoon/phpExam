<?php


namespace App\Services;

use App\DTOs\Artist\ArtistRequest;
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

    public function searchArtistByName($name) {
        return $this->repository->search($name);
    }

    public function createArtist(int $id, string $name) {
        if (empty($name) && empty($id)) {
            throw new \Exception("Missing required fields");
        }

        $request = new ArtistRequest( $id, $name);

        return $this->repository->create($request);
    }

    public function deleteArtistById($id) {
        return $this->repository->delete($id);
    }
}