<?php

namespace App\Services;

use App\DTOs\PlaylistTrack\PlaylistTrackRequest;
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

    public function create(int $playlistId, int $trackId){
        $request = new PlaylistTrackRequest($playlistId, $trackId);
        return $this->repository->create($request);
    }

    public function delete($playlistId, $trackId){
        return $this->repository->delete($playlistId, $trackId);
    }
}