<?php


namespace App\Controllers;
use App\Services\ArtistService;
use App\Services\AlbumService;
use PDO;

class AlbumController
{
    private $service;
    private $artistService;

    public function __construct(PDO $db)
    {
        $this->service = new AlbumService($db);
        $this->artistService = new ArtistService($db);
    }

    public function index()
    {
        $albums = $this->service->getAlbums();
        header('Conetent-Type: application/json');
        echo json_encode($albums);
        exit;
    }

    public function show($id)
    {
        $album = $this->service->getAlbumById($id);
        header('Conetent-Type: application/json');
        echo json_encode($album);
        exit;
    }

    public function getAlbumsByArtistId($artistId)
    {
        $albums = $this->service->getAlbumsByArtistId($artistId);
        header('Content-Type: application/json');
        echo json_encode($albums);
        exit;
    }

    public function search($searchString)
    {
        $album = $this->service->search($searchString);
        header('Content-Type: application/json');
        echo json_encode($album);
        exit;
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = json_decode(file_get_contents('php://input'), true);

            try {
                $album = $this->service->create(
                    $data['title'] ?? null,
                    $data['artist_id'] ?? null
                );

                header('Content-Type: application/json');
                http_response_code(201);
                echo json_encode($album);
                exit;
            } catch (\Exception $e) {
                http_response_code(400);
                echo json_encode($e->getMessage());
                exit;
            }
        }
    }

    public function update(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);

            try {
                $album = $this->service->update($id, $data['title'], $data['artist_id']);
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode($album);
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