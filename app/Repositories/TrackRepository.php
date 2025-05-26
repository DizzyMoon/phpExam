<?php

namespace App\Repositories;

use App\Models\Track;
use PDO;
use App\Repositories\AlbumRepository;
use App\Repositories\MediaTypeRepository;
use App\Repositories\GenreRepository;
use App\DTOs\Track\TrackRequest;

class TrackRepository
{
    private PDO $conn;
    private string $table = 'tracks';
    private AlbumRepository $albumRepo;
    private MediaTypeRepository $mediaTypeRepo;
    private GenreRepository $genreRepo;

    public function __construct(
        PDO $db,
        AlbumRepository $albumRepo,
        GenreRepository $genreRepo,
        MediaTypeRepository $mediaTypeRepo
    ) {
        $this->conn = $db;
        $this->albumRepo = $albumRepo;
        $this->mediaTypeRepo = $mediaTypeRepo;
        $this->genreRepo = $genreRepo;
    }

    public function getAll(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();

        $tracks = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $album = $this->albumRepo->getById((int) $row['albumId']);
            $genre = $this->genreRepo->getById((int) $row['genreId']);
            $mediaType = $this->mediaTypeRepo->getById((int) $row['mediaTypeId']);

            $tracks[] = new Track(
                (int) $row['trackId'],
                $row['name'],
                $album,
                $mediaType,
                $genre,
                $row['composer'],
                (int) $row['milliseconds'],
                (float) $row['bytes'],
                (float) $row['unitPrice']
            );
        }

        return $tracks;
    }

    public function getById(int $trackId): ?Track
    {
        $stmt = $this->conn->prepare('SELECT * FROM' . $this->table . ' WHERE trackId = ?');
        $stmt->execute([$trackId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $album = $this->albumRepo->getById((int) $row['albumId']);
        $genre = $this->genreRepo->getById((int) $row['genreId']);
        $mediaType = $this->mediaTypeRepo->getById((int) $row['mediaTypeId']);

        return new Track(
            $row['trackId'],
            $row['name'],
            $album,
            $mediaType,
            $genre,
            $row['composer'],
            $row['milliseconds'],
            $row['bytes'],
            $row['unitPrice']
        );
    }

    public function update(
        int $trackId,
        TrackRequest $request,
    ): bool {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table}
            SET name = ?, albumId = ?, mediaTypeId = ?, genreId = ?, composer = ?, milliseconds = ?, bytes = ?, unitPrice = ?
            WHERE trackId = ?
        ");
        return $stmt->execute([
            $request->name,
            $request->albumId,
            $request->mediaTypeId,
            $request->genreId,
            $request->composer,
            $request->milliseconds,
            $request->bytes,
            $request->unitPrice,
            $trackId
        ]);
    }

    public function create(TrackRequest $track): Track
    {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
            (name, albumId, mediaTypeId, genreId, composer, milliseconds, bytes, unitPrice)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $track->name,
            $track->albumId,
            $track->mediaTypeId,
            $track->genreId,
            $track->composer,
            $track->milliseconds,
            $track->bytes,
            $track->unitPrice
        ]);

        $trackId = (int) $this->conn->lastInsertId();

        $album = $this->albumRepo->getById((int) $track->albumId);
        $genre = $this->genreRepo->getById((int) $track->genreId);
        $mediaType = $this->mediaTypeRepo->getById((int) $track->mediaTypeId);


        return new Track(
            $trackId,
            $track->name,
            $album,
            $mediaType,
            $genre,
            $track->composer,
            $track->milliseconds,
            $track->bytes,
            $track->unitPrice
        );
    }

    public function delete(int $trackId): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE trackId = ?");
        return $stmt->execute([$trackId]);
    }
}