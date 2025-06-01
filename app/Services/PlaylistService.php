<?php

namespace App\Services;

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
}