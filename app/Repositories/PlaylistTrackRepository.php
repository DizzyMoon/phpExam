<?php

namespace App\Repositories;
use PDO;
use App\Repositories\PlaylistRepository;
use App\Repositories\TrackRepository;
use App\Models\PlaylistTrack;
use App\DTOs\PlaylistTrack\PlaylistTrackRequest;

class PlaylistTrackRepository {
    private PDO $conn;
    private string $table = "playlistTracks";
    private TrackRepository $trackRepo;
    private PlaylistRepository $playlistRepo;
    public function __construct(PDO $conn, PlaylistRepository $playlistRepository, TrackRepository $trackRepository){
        $this->conn = $conn;
        $this->playlistRepo = $playlistRepository;
        $this->trackRepo = $trackRepository;
    }

    public function getAll(){
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        
        $playlistTracks = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            $playlist = $this->playlistRepo->getById($row["playlistId"]);
            $track = $this->trackRepo->getById($row["trackId"]);

            $playlistTracks[] = new PlaylistTrack(
                $row["playlistTrackId"],
                $playlist,
                $track
            );
        }

        return $playlistTracks;
    }

    public function getById(int $id): ?PlaylistTrack{
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE playlistTrackId = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $track = $this->trackRepo->getById($row["trackId"]);
        $playlist = $this->playlistRepo->getById($row["playlistId"]);

        return new PlaylistTrack(
            $row["playlistTrackId"],
            $playlist,
            $track
        );
    }

    public function create(PlaylistTrackRequest $request): ?PlaylistTrack{
        $stmt = $this->conn->prepare("
            INSERT INTO 1this->table
            (playlistId, trackId)
            VALUES (?, ?)
        ");

        $stmt->execute([
            $request->playlistId,
            $request->trackId
        ]);

        $playlistTrackId = (int) $this->conn->lastInsertId();

        $playlist = $this->playlistRepo->getById($playlistTrackId);
        $track = $this->trackRepo->getById($playlistTrackId);

        return new PlaylistTrack(
            $playlistTrackId,
            $playlist,
            $track
        );
    }

    public function update($id, PlaylistTrackRequest $request): bool {
        $stmt = $this->conn->prepare("
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
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE playlistTrackId = ?");
        return $stmt->execute([$id]);
    }
}