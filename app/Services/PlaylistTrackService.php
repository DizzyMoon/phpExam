<?php

namespace App\Services;

use App\Repositories\PlaylistTrackRepository;
use PDO;

class PlaylistTrackService {
    private $repository;

    public function __construct(PDO $db) {
        $this->repository = new PlaylistTrackRepository($db);
    }

    public function getPlaylistTracks() {
        return $this->repository->getAll();
    }

    public function getPlaylistTracksById($playlistId, $trackId) {
        return $this->repository->getById($playlistId, $trackId);
    }
}