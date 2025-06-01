<?php

namespace App\Repositories;

require_once __DIR__ . "/../Models/Mediatype.php";

use App\DTOs\Request;
use App\DTOs\MediaType\MediaTypeRequest;
use App\Models\MediaType;

use PDO;

class MediaTypeRepository {
    private PDO $conn;

    private string $table = "MediaType";

    public function __construct(PDO $conn){
        $this->conn = $conn;
    }

    public function create(Request $request){
        if (!$request instanceof MediaTypeRequest) {
            throw new \InvalidArgumentException('Expected MediaTypeRequest');
        }

        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
            (name)
            VALUES (?)
        ");

        $stmt->execute([
            $request->name
        ]);

        $mediaTypeId = (int) $this->conn->lastInsertId();

        return new MediaType(
            $mediaTypeId,
            $request->name
        );
    }

    public function update(int $id, Request $request){
        if (!$request instanceof MediaTypeRequest) {
            throw new \InvalidArgumentException('Expected MediaTypeRequest');
        }

        $stmt = $this->conn->prepare("
            UPDATE {$this->table}
            SET name = ?
            WHERE MediaTypeId = ?
        ");

        return $stmt->execute([
            $request->name,
            $id
        ]);
    }
    public function getAll(): array{
        $stmt = $this->conn->prepare("
            SELECT * FROM {$this->table}
        ");
        $stmt->execute();
        
        $mediaTypes = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mediaTypes[] = new MediaType(
                $row["MediaTypeId"],
                $row["Name"]
            );
        }

        return $mediaTypes;
    }

    public function getById(int $id){
        $stmt = $this->conn->prepare("
            SELECT * FROM {$this->table} WHERE MediaTypeId = ?
        ");
        $stmt->execute([
            $id
        ]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null; 
        }

        return new MediaType(
            $row["MediaTypeId"],
            $row["Name"]
        );
    }
    public function delete(int $id): bool{
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE MediaTypeId = ?");
        return $stmt->execute([
            $id
        ]);
    }
}