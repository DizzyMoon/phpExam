<?php

namespace App\Controllers;

use App\Services\TrackService;
use PDO;

class TrackController {
    private $service;

    public function __construct(PDO $db){
        $this->service = new TrackService($db);
    }

    public function index() {
        $tracks = $this->service->getTracks();
        header("Content-Type: applications/json");
        echo json_encode($tracks);
        exit;
    }

    public function show($id) {
        $track = $this->service->getTrackById($id);
        header("Content-Type: application/json");
        echo json_encode($track);
        exit;
    }
}