<?php

namespace App\Controllers;
use App\Services\MediaTypeService;
use PDO;

class MediaTypeController {
    private $service;

    public function __construct(PDO $db) {
        $this->service = new MediaTypeService($db);
    }

    public function index() {
        $mediaTypes = $this->service->getMediaTypes();
        header('Content-Type: application/json');
        echo json_encode($mediaTypes);
        exit;
    }

    public function show($id) {
        $mediaType = $this->service->getMediaTypesById($id);
        header('Content-Type: application/json');
        echo json_encode($mediaType);
        exit;
    }
}