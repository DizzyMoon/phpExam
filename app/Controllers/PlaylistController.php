<?php

namespace App\Controllers;

use App\Services\PlaylistService;
use PDO;

class PlaylistController {
    private $service;

    public function __construct(PDO $db) {
        $this->service = new PlaylistService($db);
    }

    public function index() {
        $playlists = $this->service->getPlaylists();
        header('Content-Type: application/json');
        echo json_encode($playlists);
        exit;
    }

    public function show($id) {
        $playlist = $this->service->getPlaylistById($id);
        header('Content-Type: application/json');
        echo json_encode($playlist);
        exit;
    }
}