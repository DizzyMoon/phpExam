<?php

namespace App\Repositories;
use App\DTOs\Artist\ArtistRequest;
use PDO;
use App\Models\Artist;



class ArtistRepository {
    private PDO $conn;
    private string $table = 'Artist';
    
    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function getAll() : array {
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

    public function getById(int $id) : ?Artist {
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

    public function create(ArtistRequest $request) : Artist {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
            (name)
            VALUES (?, ?)
        ");
        $stmt->execute([
            $request->name
        ]);
        $artistId = (int) $this->conn->lastInsertId();

        return new Artist(
            (int) $artistId,
            $request->name
        );
    }

    public function delete(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table}");
        return $stmt->execute([$id]);
    }

    public function update($id, ArtistRequest $request) : bool {
        $stmt = $this->conn->prepare("
            UPDATE this->table
            SET name = ?
            WHERE artistId = ?
        ");

        return $stmt->execute([
            $request->name,
            $id
        ]);
    }
}