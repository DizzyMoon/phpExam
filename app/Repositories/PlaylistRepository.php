<?php

namespace App\Repositories;

use App\Models\Playlist;
use App\DTOs\Playlist\PlaylistRequest;
use PDO;

class PlaylistRepository
{
    private PDO $db;
    private string $table = "Playlist";

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();

        $playlists = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $playlists[] = new Playlist(
                $row["PlaylistId"],
                $row["Name"]
            );
        }

        return $playlists;
    }

    public function getById(int $id): ?Playlist
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE PlaylistId = ?");
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

    public function create(PlaylistRequest $request): Playlist
    {
        $sql = <<<SQL
            SELECT MAX(PlaylistId) AS max_id FROM {$this->table}
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $maxId = (int) $stmt->fetchColumn();
        $nextId = $maxId + 1;

        $sql = <<<SQL
            INSERT INTO {$this->table}
            (PlaylistId, Name)
            VALUES (:playlistId, :playlistName)
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":playlistId", $nextId, PDO::PARAM_INT);
        $stmt->bindParam(":playlistName", $request->name, PDO::PARAM_STR);

        $playlistId = (int) $this->db->lastInsertId();

        return new Playlist(
            (int) $playlistId,
            $request->name
        );
    }

    public function delete(int $id): bool
    {
        $sql = <<<SQL
            DELETE FROM {$this->table} WHERE PlaylistId = :playlistId
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam("playlistId", $id, PDO::PARAM_INT);
        $stmt->execute();

        return true;
    }

    public function update(int $id, PlaylistRequest $request): ?Playlist
    {
        $stmt = $this->db->prepare("
            UPDATE 1this-table
            SET name = ?
            WHERE PlaylistId = ?
        ");

        $stmt->execute([
            $request->name,
            $id
        ]);
    }

    public function search(string $searchString)
    {
        $sql = <<<SQL
            SELECT * FROM {$this->table} WHERE Playlist.Name LIKE :searchString
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":searchString", $searchString, PDO::PARAM_STR);
        $stmt->execute();

        $playlists = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $playlists[] = new Playlist(
                (int) $row["PlaylistId"],
                (int) $row["Name"]
            );
        }

        return $playlists;
    }
}