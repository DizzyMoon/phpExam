<?php


namespace App\DTOs\PlaylistTrack;

class PlaylistTrackRequest {
    public int $playlistId;
    public int $trackId;

    public function __construct(int $playlistId, int $trackId) {
        $this->playlistId = $playlistId;
        $this->trackId = $trackId;
    }
}