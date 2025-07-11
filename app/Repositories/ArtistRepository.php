<?php

namespace App\Repositories;
use App\DTOs\Artist\ArtistRequest;
use Exception;
use PDO;
use App\Models\Artist;
use PDOException;

class ArtistRepository
{
    private PDO $conn;
    private string $table = 'Artist';

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getAll(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();

        $artists = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $artists[] = new Artist(
                $row["ArtistId"],
                $row["Name"]
            );
        }

        return $artists;
    }

    public function search(string $searchParam)
    {
        $sql = <<<SQL
            SELECT * FROM {$this->table} WHERE Artist.Name LIKE :searchParam
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":searchParam", $searchParam, PDO::PARAM_STR);
        $stmt->execute();

        $artists = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $artists[] = new Artist(
                $row["ArtistId"],
                $row["Name"]
            );

        }

        return $artists;
    }
    public function getById(int $id): ?Artist
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE ArtistId = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Artist(
            (int) $row["ArtistId"],
            $row["Name"]
        );
    }

    public function create(ArtistRequest $request): Artist
    {
        $sql = <<<SQL
            SELECT MAX(ArtistId) AS max_id FROM {$this->table}
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $maxId = (int) $stmt->fetchColumn();
        $nextId = $maxId + 1;

        $sql = <<<SQL
            INSERT INTO {$this->table}
            (ArtistId, Name)
            VALUES (:artistId, :artistName)
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":artistId", $nextId, PDO::PARAM_INT);
        $stmt->bindParam(":artistName", $request->name, PDO::PARAM_STR);

        try {
            $stmt->execute();

            $artistId = $this->conn->lastInsertId();

            return new Artist($artistId, $request->name);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }

    }

    public function delete(int $id): bool
    {
        $sql = <<<SQL
            DELETE FROM {$this->table} WHERE ArtistId = :artistId
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":artistId", $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($id, ArtistRequest $request): bool
    {
        $sql = <<<SQL
            UPDATE {$this->table}
            SET name = :artistName
            WHERE ArtistId = :artistId
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":artistName", $id, PDO::PARAM_STR);
        $stmt->bindParam(":artistId", $request->name, PDO::PARAM_INT);
        return $stmt->execute();
    }
}