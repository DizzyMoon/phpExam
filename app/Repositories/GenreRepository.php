<?php

namespace App\Repositories;

use App\DTOs\Request;
use App\DTOs\Genre\GenreRequest;
use App\Models\Genre;

use PDO;

class GenreRepository {

    private PDO $conn;
    private string $table = "Genre";

    public function __construct(PDO $db){
        $this->conn = $db;
    }

    public function create(Request $request){
        if (!$request instanceof GenreRequest){
            throw new \InvalidArgumentException("Expected GenreRequest");
        }

        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
            (name)
            VALUES (?)
        ");
        $stmt->execute([
            $request->name
        ]);

        $trackId = (int) $this->conn->lastInsertId();

        return new Genre(
            $trackId,
            $request->name
        );
    }
    public function update(int $id, Request $request): bool{
        if (!$request instanceof GenreRequest){
            throw new \InvalidArgumentException("Expected GenreRequest");
        }

        $stmt = $this->conn->prepare("
            UPDATE {$this->table}
            SET name = ?
            WHERE genreId = ?
        ");

        return $stmt->execute([
            $request->name,
            $id
        ]);
    }
    public function getAll(): array{
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();

        $genres = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $genres[] = new Genre(
                $row["GenreId"],
                $row["Name"]
            );
        }

        return $genres;
    }
    public function getById(int $id){
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE GenreId = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Genre(
            $row["GenreId"],
            $row["Name"]
        );
    }

    public function delete(int $id): bool{
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE GenreId = ?");
        return $stmt->execute([$id]);
    }
}