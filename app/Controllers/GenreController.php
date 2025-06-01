<?php


namespace App\Controllers;
use App\Services\GenreService;
use PDO;

class GenreController {
    private $service;

    public function __construct(PDO $db) {
        $this->service = new GenreService($db);
    }

    public function index() {
        $genres = $this->service->getAllGenres();
        header('Content-Type: application/json');
        echo json_encode($genres);
        exit;
    }

    public function show($id) {
        $genre = $this->service->getGenreById($id);
        header('Content-Type: application/json');
        echo json_encode($genre);
        exit;
    }
}