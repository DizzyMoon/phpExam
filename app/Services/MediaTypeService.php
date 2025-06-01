<?php

namespace App\Services;

use App\Repositories\MediaTypeRepository;
use PDO;

class MediaTypeService {
    private $repository;

    public function __construct(PDO $db) {
        $this->repository = new MediaTypeRepository($db);
    }

    public function getMediaTypes() {
        return $this->repository->getAll();
    }

    public function getMediaTypesById($id) {
        return $this->repository->getById($id);
    }
}