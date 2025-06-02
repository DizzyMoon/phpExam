<?php


namespace App\Controllers;
use App\Services\ArtistService;
use PDO;

class ArtistController {
    private $service;

    public function __construct(PDO $db) {
        $this->service = new ArtistService($db);
    }

    public function index() {
        $artists = $this->service->getArtists();
        header('Content-Type: application/json');
        echo json_encode($artists);
        exit;
    }

    public function show($id) {
        $artist = $this->service->getArtistById($id);
        header('Content-Type: application/json');
        echo json_encode($artist);
        exit;
    }

    public function search($name) {
        $artists = $this->service->searchArtistByName($name);
        header('Content-Type: application/json');
        echo json_encode($artists);
        exit;
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = json_decode(file_get_contents('php://input'), true);

            try {
                $artist = $this->service->createArtist(
                    $data['name'] ?? null
                );

                header(header: 'Content-Type: application/json');
                http_response_code(201);
                echo json_encode($artist);
                exit;
            } catch (\Exception $e) {
                http_response_code(400);
                echo json_encode(['error'=> $e->getMessage()]);
                exit;
            }
        }
    }

    public function delete($id) {
        return $this->service->deleteArtistById($id);
    }
}

