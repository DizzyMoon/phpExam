<?php

namespace App\Controllers;

use App\Services\PlaylistTrackService;
use PDO;

class PlaylistTrackController {
    private $service;

    public function __construct(PDO $db) {
        $this->service = new PlaylistTrackService($db);
    }

    public function index() {
        $playlistTracks = $this->service->getPlaylistTracks();
        header("Content-Type: application/json");
        echo json_encode($playlistTracks);
        exit;
    }

    public function show($playlistTrack, $trackId) {
        $playlistTrack = $this->service->getPlaylistTracksById($playlistTrack, $trackId);
        header("Content-Type: application/json");
        echo json_encode($playlistTrack);
        exit;
    }
}