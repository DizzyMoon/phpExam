<?php

namespace App\Services;

require_once __DIR__ . "/../Models/Track.php";

use \App\Repositories\TrackRepository;
use \App\Models\Track;

class TrackService {
    private $repository;

    public function __construct($db) {
        $this->repository = new TrackRepository($db);
    }

    public function createTrack() {
        
    }

    public function getTrack($id) {

    }
}