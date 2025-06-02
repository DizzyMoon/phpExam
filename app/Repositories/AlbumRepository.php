<?php

namespace App\Repositories;
use PDO;
use App\DTOs\Album\AlbumRequest;
use App\Repositories\ArtistRepository;
use App\Models\Album;

class AlbumRepository
{
    private PDO $conn;
    private string $table = 'Album';
    private ArtistRepository $artistRepository;

    public function __construct(
        PDO $db,
    ) {
        $this->artistRepository = new ArtistRepository($db);
        $this->conn = $db;
    }

    public function getAll(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();

        $albums = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $artist = $this->artistRepository->getById((int) $row["ArtistId"]);

            $albums[] = new Album(
                (int) $row["AlbumId"],
                $row["Title"],
                $artist
            );
        }

        return $albums;
    }

    public function getAlbumsByArtistId(int $artistId)
    {
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

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $artist = $this->artistRepository->getById((int) $row["ArtistId"]);
            $albums[] = new Album(
                $row["AlbumId"],
                $row["Title"],
                $artist
            );
        }

        return $albums;
    }

    public function search($searchString)
    {
        $sql = <<<SQL
            SELECT * FROM {$this->table} WHERE Album.Title LIKE :searchString
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":searchString", $searchString, PDO::PARAM_STR);

        $stmt->execute();

        $albums = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $artist = $this->artistRepository->getById((int) $row["ArtistId"]);

            $albums[] = new Album(
                (int) $row["AlbumId"],
                $row["Title"],
                $artist
            );
        }

        return $albums;
    }

    public function getById(int $id): ?Album
    {
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

    public function create(AlbumRequest $request)
    {

        $sql = <<<SQL
            SELECT MAX(AlbumId) AS max_id FROM {$this->table}
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $maxId = (int) $stmt->fetchColumn();
        $nextId = $maxId + 1;


        $sql = <<<SQL
            INSERT INTO {$this->table}
            (AlbumId, Title, ArtistId)
            VALUES (:albumId, :albumTitle, :artistId)
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":albumId", $nextId, PDO::PARAM_INT);
        $stmt->bindParam(":albumTitle", $request->title, PDO::PARAM_STR);
        $stmt->bindParam(":artistId", $request->artistId, PDO::PARAM_INT);
        $stmt->execute();

        $albumId = (int) $this->conn->lastInsertId();

        $artist = $this->artistRepository->getById((int) $albumId);

        return new Album(
            (int) $albumId,
            $request->title,
            $artist
        );
    }

    public function delete(int $id): bool
    {
        try {
            $this->conn->beginTransaction();

            $sql = <<<SQL
                DELETE il FROM InvoiceLine il
                JOIN Track t ON il.TrackId = t.TrackId
                WHERE t.AlbumId = :albumId
            SQL;
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":albumId", $id, PDO::PARAM_INT);
            $stmt->execute();

            $sql = <<<SQL
                DELETE pt FROM PlaylistTrack pt
                JOIN Track t ON pt.TrackId = t.TrackId
                WHERE t.AlbumId = :albumId
            SQL;
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":albumId", $id, PDO::PARAM_INT);
            $stmt->execute();

            $sql = <<<SQL
                DELETE FROM Track WHERE AlbumId = :albumId
            SQL;
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":albumId", $id, PDO::PARAM_INT);
            $stmt->execute();

            $sql = <<<SQL
            DELETE FROM {$this->table} WHERE AlbumId = :albumId
            SQL;
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":albumId", $id, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();

            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function update(int $albumId, AlbumRequest $request): bool
    {
        $sql = <<<SQL
            UPDATE {$this->table}
            SET Title = :title, ArtistId = :artistId
            WHERE AlbumId = :albumId
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':title', $request->title, PDO::PARAM_STR);
        $stmt->bindParam(':artistId', $request->artistId, PDO::PARAM_INT);
        $stmt->bindParam(':albumId', $albumId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}