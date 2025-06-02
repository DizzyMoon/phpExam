<?php

namespace App\Controllers;

use App\Services\TrackService;
use PDO;

class TrackController
{
    private $service;

    public function __construct(PDO $db)
    {
        $this->service = new TrackService($db);
    }

    public function index()
    {
        $tracks = $this->service->getTracks();
        header("Content-Type: applications/json");
        echo json_encode($tracks);
        exit;
    }

    public function show($id)
    {
        $track = $this->service->getTrackById($id);
        header("Content-Type: application/json");
        echo json_encode($track);
        exit;
    }

    public function getTracksByAlbumId($albumId)
    {
        $tracks = $this->service->getTracksByAlbumId($albumId);
        header("Content-Type: application/json");
        echo json_encode($tracks);
        exit;
    }

    public function search(string $searchString)
    {
        $tracks = $this->service->search($searchString);
        header("Content-Type: application/json");
        echo json_encode($tracks);
        exit;
    }

    public function getByComposer(string $composer)
    {
        $tracks = $this->service->getTracksByComposer($composer);
        header("Content-Type: application/json");
        echo json_encode($tracks);
        exit;
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);

            try {
                $track = $this->service->createTrack(
                    $data['name'],
                    $data['album_id'],
                    $data['media_type_id'],
                    $data['genre_id'],
                    $data['composer'],
                    $data['milliseconds'],
                    $data['bytes'],
                    $data['unit_price']
                );

                header(header: 'Content-Type: application/json');
                http_response_code(201);
                echo json_encode($track);
                exit;
            } catch (\Exception $e) {
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
                exit;
            }
        }
    }

    public function update(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);

            try {
                $track = $this->service->update(
                    $id,
                    $data['name'],
                    $data['album_id'],
                    $data['media_type_id'],
                    $data['genre_id'],
                    $data['composer'],
                    $data['milliseconds'],
                    $data['bytes'],
                    $data['unit_price']
                );
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode($track);
                exit;
            } catch (\Exception $e) {
                http_response_code(400);
                echo json_encode($e->getMessage());
                exit;
            }
        }
    }

    public function delete(int $id)
    {
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