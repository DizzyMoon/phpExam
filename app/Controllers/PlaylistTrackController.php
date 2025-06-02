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

    public function store($playlistId) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = json_decode(file_get_contents('php://input'), true);

            try {
                $playlistTrack = $this->service->create(
                    $playlistId,
                    $data['track_id'] ?? null
                );

                header('Content-Type: application/json');
                http_response_code(201);
                echo json_encode($playlistTrack);
                exit;
            } catch (\Exception $e) {
                http_response_code(400);
                echo json_encode($e->getMessage());
                exit;
            }
        }
    }

    public function delete ($playlistId, $trackId) {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            try {
                $response = $this->service->delete($playlistId, $trackId);
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } catch (\Exception $e) {
                http_response_code(400);
                echo json_encode($e->getMessage());
                exit;
            }
        }
    }
}