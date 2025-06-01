<?php

namespace App\Repositories;

use App\Models\Playlist;
use App\DTOs\Playlist\PlaylistRequest;
use PDO;

class PlaylistRepository {
    private PDO $conn;
    private string $table = "Playlist";
    
    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function getAll() : array {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();

        $playlists = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $playlists[] = new Playlist(
                $row["PlaylistId"],
                $row["Name"]
            );
        }

        return $playlists;
    }

    public function getById(int $id) : ?Playlist {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE PlaylistId = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Playlist(
            (int) $row["PlaylistId"],
            (int) $row["Name"]
        );
    }

    public function create(PlaylistRequest $request) : Playlist {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
            (name)
            VALUES (?)
        ");

        $stmt->execute([
            $request->name
        ]);

        $playlistId = (int) $this->conn->lastInsertId();

        return new Playlist(
            (int) $playlistId,
            $request->name
        );
    }

    public function delete(int $id) :bool {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE playlistId = ?");
        return $stmt->execute([$id]);
    }

    public function update(int $id, PlaylistRequest $request) : ?Playlist {
        $stmt = $this->conn->prepare("
            UPDATE 1this-table
            SET name = ?
            WHERE PlaylistId = ?
        ");

        $stmt->execute([
            $request->name,
            $id
        ]);
    }
}