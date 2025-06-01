<?php

namespace App\Repositories;

use App\Models\Track;
use PDO;
use App\Repositories\AlbumRepository;
use App\Repositories\MediaTypeRepository;
use App\Repositories\GenreRepository;
use App\DTOs\Track\TrackRequest;
use App\Repositories\Repository;

class TrackRepository
{
    private PDO $conn;
    private string $table = 'Track';
    private AlbumRepository $albumRepo;
    private MediaTypeRepository $mediaTypeRepo;
    private GenreRepository $genreRepo;

    public function __construct(
        PDO $db,
    ) {
        $this->conn = $db;
        $this->albumRepo = new AlbumRepository($db);
        $this->mediaTypeRepo = new MediaTypeRepository($db);
        $this->genreRepo = new GenreRepository($db);
    }

    public function getAll(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();

        $tracks = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $album = $this->albumRepo->getById((int) $row['AlbumId']);
            $genre = $this->genreRepo->getById((int) $row['GenreId']);
            $mediaType = $this->mediaTypeRepo->getById((int) $row['MediaTypeId']);

            $tracks[] = new Track(
                (int) $row['TrackId'],
                $row['Name'],
                $album,
                $mediaType,
                $genre,
                $row['Composer'],
                (int) $row['Milliseconds'],
                (float) $row['Bytes'],
                (float) $row['UnitPrice']
            );
        }

        return $tracks;
    }

    public function getById(int $trackId): ?Track
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE TrackId = ?");
        $stmt->execute([$trackId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $album = $this->albumRepo->getById((int) $row['AlbumId']);
        $mediaType = $this->mediaTypeRepo->getById((int) $row['MediaTypeId']);
        $genre = $this->genreRepo->getById((int) $row['GenreId']);

        return new Track(
            $row['TrackId'],
            $row['Name'],
            $album,
            $mediaType,
            $genre,
            $row['Composer'],
            $row['Milliseconds'],
            $row['Bytes'],
            $row['UnitPrice']
        );
    }

    public function update(
        int $id,
        TrackRequest $request
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
            $id
        ]);
    }

    public function create(TrackRequest $request): Track
    {

        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
            (name, albumId, mediaTypeId, genreId, composer, milliseconds, bytes, unitPrice)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $request->name,
            $request->albumId,
            $request->mediaTypeId,
            $request->genreId,
            $request->composer,
            $request->milliseconds,
            $request->bytes,
            $request->unitPrice
        ]);

        $trackId = (int) $this->conn->lastInsertId();

        $album = $this->albumRepo->getById((int) $request->albumId);
        $genre = $this->genreRepo->getById((int) $request->genreId);
        $mediaType = $this->mediaTypeRepo->getById((int) $request->mediaTypeId);


        return new Track(
            $trackId,
            $request->name,
            $album,
            $mediaType,
            $genre,
            $request->composer,
            $request->milliseconds,
            $request->bytes,
            $request->unitPrice
        );
    }

    public function delete(int $trackId): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE trackId = ?");
        return $stmt->execute([$trackId]);
    }
}