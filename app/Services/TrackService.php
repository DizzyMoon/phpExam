<?php

namespace App\Services;
use App\DTOs\Track\TrackRequest;

require_once __DIR__ . "/../Models/Track.php";

use \App\Repositories\TrackRepository;
use \App\Models\Track;
use \App\Repositories\AlbumRepository;
use \App\Repository\GenreRepository;
use \App\Config\Database;

class TrackService
{
    private $repository;

    public function __construct($db)
    {
        $this->repository = new TrackRepository($db);
    }

    public function createTrack(
        string $name,
        int $albumId,
        int $mediaTypeId,
        int $genreId,
        string $composer,
        int $milliseconds,
        float $bytes,
        float $unitPrice
    ) {
        $request = new TrackRequest(
            $name,
            $albumId,
            $mediaTypeId,
            $genreId,
            $composer,
            $milliseconds,
            $bytes,
            $unitPrice
        );

        return $this->repository->create($request);
    }

    public function getTracks()
    {
        return $this->repository->getAll();
    }

    public function getTrackById($id)
    {
        return $this->repository->getById($id);
    }

    public function getTracksByAlbumId($albumId)
    {
        return $this->repository->getTracksByAlbumId($albumId);
    }

    public function search(string $searchString)
    {
        return $this->repository->search($searchString);
    }

    public function getTracksByComposer(string $composer)
    {
        return $this->repository->getByComposer($composer);
    }

    public function update(
        int $id,
        string $name,
        int $albumId,
        int $mediaTypeId,
        int $genreId,
        string $composer,
        int $milliseconds,
        float $bytes,
        int $unitPrice
    ) {
        $request = new TrackRequest(
            $name,
            $albumId,
            $mediaTypeId,
            $genreId,
            $composer,
            $milliseconds,
            $bytes,
            $unitPrice
        );
        $this->repository->update($id, $request);
    }

    public function delete(int $id) {
        return $this->repository->delete($id);
    }
}