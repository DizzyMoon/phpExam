<?php

namespace App\Controllers;

use App\Services\PlaylistService;
use PDO;

class PlaylistController
{
    private $service;

    public function __construct(PDO $db)
    {
        $this->service = new PlaylistService($db);
    }

    public function index()
    {
        $playlists = $this->service->getPlaylists();
        header('Content-Type: application/json');
        echo json_encode($playlists);
        exit;
    }

    public function show($id)
    {
        $playlist = $this->service->getPlaylistById($id);
        header('Content-Type: application/json');
        echo json_encode($playlist);
        exit;
    }

    public function search(string $searchString)
    {
        $playlists = $this->service->search($searchString);
        header('Content-Type: application/json');
        echo json_encode($playlists);
        exit;
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);

            try {
                $playlist = $this->service->create(
                    $data['name'] ?? null
                );

                header('Content-Type: application/json');
                http_response_code(201);
                echo json_encode($playlist);
                exit;
            } catch (\Exception $e) {
                http_response_code(400);
                echo json_encode($e->getMessage());
                exit;
            }
        }
    }

    public function delete ($id){
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            try {
                $response = $this->service->delete($id);
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