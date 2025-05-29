<?php

namespace App\Repositories;
use PDO;
use App\DTOs\Album\AlbumRequest;
use App\Repositories\ArtistRepository;
use App\Models\Album;

class AlbumRepository {
    private PDO $conn;
    private string $table = 'albums';
    private ArtistRepository $artistRepository;

    public function __construct(
        PDO $conn,
        ArtistRepository $artistRepository
        ) {
            $this->conn = $conn;
            $this->artistRepository = $artistRepository;
        }

    public function getAll(): array {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();

        $albums = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $artist = $this->artistRepository->getById((int) $row["artistId"]);

            $albums[] = new Album(
                (int) $row["albumId"],
                 $row["title"],
                $artist
            );
        }

        return $albums;
    }

    public function getById(int $id): ?Album {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE trackId = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $artist = $this->artistRepository->getById((int) $row["artistId"]);

        return new Album(
            (int) $row["albumId"],
            $row["title"],
            $artist
        );
    }

    public function create(AlbumRequest $request) {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
            (title, artistId)
            VALUES (?, ?)
        ");
        $stmt->execute([
            $request->title,
            $request->artistId
        ]);

        $albumId = (int) $this->conn->lastInsertId();

        $artist = $this->artistRepository->getById((int) $albumId);

        return new Album(
            (int) $albumId,
            $request->title,
            $artist
        );
    }

    public function delete(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE albumId = ?");
        return $stmt->execute([$id]);
    }
}