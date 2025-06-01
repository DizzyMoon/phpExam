<?php


namespace App\Controllers;
use App\Services\ArtistService;
use App\Services\AlbumService;
use PDO;

class AlbumController {
    private $service;
    private $artistService;

    public function __construct(PDO $db) {
        $this->service = new AlbumService($db);
        $this->artistService = new ArtistService($db);
    }

    public function index() {
        $albums = $this->service->getAlbums();
        header('Conetent-Type: application/json');
        echo json_encode($albums);
        exit;
    }

    public function show($id) {
        $album = $this->service->getAlbumById($id);
        header('Conetent-Type: application/json');
        echo json_encode($album);
        exit;
    }
}