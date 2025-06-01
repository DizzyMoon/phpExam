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
}

