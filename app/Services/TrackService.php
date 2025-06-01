<?php

namespace App\Services;
use App\DTOs\Track\TrackRequest;

require_once __DIR__ . "/../Models/Track.php";

use \App\Repositories\TrackRepository;
use \App\Models\Track;
use \App\Repositories\AlbumRepository;
use \App\Repository\GenreRepository;
use \App\Config\Database;

class TrackService {
    private $repository;

    public function __construct($db) {
        $this->repository = new TrackRepository($db);
    }

    public function createTrack(TrackRequest $request) {
        return $this->repository->create($request);
    }

    public function getTracks() {
        return $this->repository->getAll();
    }

    public function getTrackById($id) {
        return $this->repository->getById($id);
    }
}