<?php

namespace App\Repositories;
use PDO;
use App\Repositories\PlaylistRepository;
use App\Repositories\TrackRepository;
use App\Models\PlaylistTrack;
use App\DTOs\PlaylistTrack\PlaylistTrackRequest;

class PlaylistTrackRepository {
    private PDO $db;
    private string $table = "PlaylistTrack";
    private TrackRepository $trackRepo;
    private PlaylistRepository $playlistRepo;
    public function __construct(PDO $db){
        $this->db = $db;
        $this->playlistRepo = new PlaylistRepository($db);
        $this->trackRepo = new TrackRepository($db);
    }

    public function getAll(){
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        
        $playlistTracks = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            $playlist = $this->playlistRepo->getById($row["PlaylistId"]);
            $track = $this->trackRepo->getById($row["TrackId"]);

            $playlistTracks[] = new PlaylistTrack(
                $playlist,
                $track
            );
        }

        return $playlistTracks;
    }

    public function getById(int $playlistId, int $trackId): ?PlaylistTrack{


        $sql = <<<SQL
            SELECT * FROM {$this->table} WHERE PlaylistId = :playlistId AND TrackId = :trackId;
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':playlistId', $playlistId, PDO::PARAM_INT);
        $stmt->bindParam(':trackId', $trackId, PDO::PARAM_INT);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $track = $this->trackRepo->getById($row["TrackId"]);
        $playlist = $this->playlistRepo->getById($row["PlaylistId"]);

        return new PlaylistTrack(
            $playlist,
            $track
        );
    }

    public function create(PlaylistTrackRequest $request): ?PlaylistTrack{
        $sql = <<<SQL
            SELECT MAX(PlayListTrackId) AS max_id FROM {$this->table}
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $maxId = (int) $stmt->fetchColumn();
        $nextId = $maxId + 1;
        
        $sql = <<<SQL
            SELECT MAX(PlayListTrackId) AS max_id FROM {$this->table}
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $maxId = (int) $stmt->fetchColumn();
        $nextId = $maxId + 1;

        $sql = <<<SQL
            INSERT INTO {$this->table} (PlaylistId, TrackId)
            VALUES (:playlistId, :trackId)
            SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':playlistId:', $request->playlistId, PDO::PARAM_INT);
        $stmt->bindParam(':trackId:', $request->trackId, PDO::PARAM_INT);

        $stmt->execute();

        $playlistTrackId = (int) $this->db->lastInsertId();

        $playlist = $this->playlistRepo->getById($playlistTrackId);
        $track = $this->trackRepo->getById($playlistTrackId);

        return new PlaylistTrack(
            $playlist,
            $track
        );
    }

    public function update($id, PlaylistTrackRequest $request): bool {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET playlistId = ?, trackId = ?
            WHERE playlistTrackId = ?
        
        ");
        return $stmt->execute([
            $request->playlistId,
            $request->trackId,
            $id
        ]);
    }

    public function delete($playlistId, $trackId): bool {
        $sql = <<<SQL
            DELETE FROM {$this->table} WHERE PlaylistId = :playlistId AND TrackId = :trackId
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":playlistId", $playlistId, PDO::PARAM_INT);
        $stmt->bindParam(":trackId", $trackId, PDO::PARAM_INT);

        $stmt->execute();

        return true;
    }
}