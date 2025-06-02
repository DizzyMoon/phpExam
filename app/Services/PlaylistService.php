<?php

namespace App\Services;

use App\DTOs\Playlist\PlaylistRequest;
use App\Repositories\PlaylistRepository;
use PDO;

class PlaylistService {
    private $repository;

    public function __construct(PDO $db) {
        $this->repository = new PlaylistRepository($db);
    }

    public function getPlaylists() {
        return $this->repository->getAll();
    }

    public function getPlaylistById($id) {
        return $this->repository->getById($id);
    }

    public function search(string $searchString) {
        return $this->repository->search($searchString);
    }

    public function create(string $name) {
        $request = new PlaylistRequest($name);
        return $this->repository->create($request);
    }

    public function delete(int $id) {
        return $this->repository->delete($id);
    }
}