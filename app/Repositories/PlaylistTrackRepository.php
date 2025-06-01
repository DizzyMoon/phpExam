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
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE PlaylistId = ? AND TrackId = ?");
        $stmt->execute([$playlistId, $trackId]);
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
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
            (playlistId, trackId)
            VALUES (?, ?)
        ");

        $stmt->execute([
            $request->playlistId,
            $request->trackId
        ]);

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

    public function delete($id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE PlaylistTrackId = ?");
        return $stmt->execute([$id]);
    }
}