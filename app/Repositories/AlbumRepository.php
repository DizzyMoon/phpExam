<?php

namespace App\Repositories;
use PDO;
use App\DTOs\Album\AlbumRequest;
use App\Repositories\ArtistRepository;
use App\Models\Album;

class AlbumRepository {
    private PDO $conn;
    private string $table = 'Album';
    private ArtistRepository $artistRepository;

    public function __construct(
        PDO $db,
        ) {
            $this->artistRepository = new ArtistRepository($db);
            $this->conn = $db;
        }

    public function getAll(): array {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();

        $albums = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $artist = $this->artistRepository->getById((int) $row["ArtistId"]);

            $albums[] = new Album(
                (int) $row["AlbumId"],
                 $row["Title"],
                $artist
            );
        }

        return $albums;
    }

    public function getAlbumsByArtistId(int $artistId) {
        $sql = <<<SQL
            SELECT {$this->table}.*
            FROM {$this->table}
            Join Artist ON Album.ArtistId = Artist.ArtistId
            WHERE Artist.ArtistId = :artistId
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":artistId", $artistId, PDO::PARAM_INT);
        $stmt->execute();

        
        $albums = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $artist = $this->artistRepository->getById((int) $row["ArtistId"]);
            $albums[] = new Album(
                $row["AlbumId"],
                $row["Title"],
                $artist
            );
        }

        return $albums;
    }

    public function search($searchString) {
        $sql = <<<SQL
            SELECT * FROM {$this->table} WHERE Album.Title Like :searchString
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":searchString", $searchString, PDO::PARAM_STR);

        $stmt->execute();

        $albums = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $artist = $this->artistRepository->getById((int) $row["ArtistId"]);

            $albums[] = new Album(
                (int) $row["AlbumId"],
                $row["Title"],
                $artist
            );
        }

        return $albums;
    }

    public function getById(int $id): ?Album {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE AlbumId = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $artist = $this->artistRepository->getById((int) $row["ArtistId"]);

        return new Album(
            (int) $row["AlbumId"],
            $row["Title"],
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
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE AlbumId = ?");
        return $stmt->execute([$id]);
    }
}