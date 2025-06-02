<?php

namespace App\Repositories;

use App\Models\Track;
use PDO;
use App\Repositories\AlbumRepository;
use App\Repositories\MediaTypeRepository;
use App\Repositories\GenreRepository;
use App\DTOs\Track\TrackRequest;
use App\Repositories\Repository;

class TrackRepository
{
    private PDO $conn;
    private string $table = 'Track';
    private AlbumRepository $albumRepo;
    private MediaTypeRepository $mediaTypeRepo;
    private GenreRepository $genreRepo;

    public function __construct(
        PDO $db,
    ) {
        $this->conn = $db;
        $this->albumRepo = new AlbumRepository($db);
        $this->mediaTypeRepo = new MediaTypeRepository($db);
        $this->genreRepo = new GenreRepository($db);
    }

    public function getAll(): array
    {
        $sql = <<<SQL
            SELECT * FROM {$this->table}
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $tracks = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $album = $this->albumRepo->getById((int) $row['AlbumId']);
            $genre = $this->genreRepo->getById((int) $row['GenreId']);
            $mediaType = $this->mediaTypeRepo->getById((int) $row['MediaTypeId']);

            $tracks[] = new Track(
                (int) $row['TrackId'],
                $row['Name'],
                $album,
                $mediaType,
                $genre,
                $row['Composer'],
                (int) $row['Milliseconds'],
                (float) $row['Bytes'],
                (float) $row['UnitPrice']
            );
        }

        return $tracks;
    }

    public function getById(int $trackId): ?Track
    {
        $sql = <<<SQL
            SELECT * FROM {$this->table} WHERE TrackId = :trackId
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':trackId', $trackId, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $album = $this->albumRepo->getById((int) $row['AlbumId']);
        $mediaType = $this->mediaTypeRepo->getById((int) $row['MediaTypeId']);
        $genre = $this->genreRepo->getById((int) $row['GenreId']);

        return new Track(
            $row['TrackId'],
            $row['Name'],
            $album,
            $mediaType,
            $genre,
            $row['Composer'],
            $row['Milliseconds'],
            $row['Bytes'],
            $row['UnitPrice']
        );
    }

    public function getTracksByAlbumId(int $albumId)
    {
        $sql = <<<SQL
            SELECT * FROM {$this->table} WHERE AlbumId = :albumId
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':albumId', $albumId, PDO::PARAM_INT);
        $stmt->execute();

        $tracks = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $album = $this->albumRepo->getById((int) $row['AlbumId']);
            $genre = $this->genreRepo->getById((int) $row['GenreId']);
            $mediaType = $this->mediaTypeRepo->getById((int) $row['MediaTypeId']);

            $tracks[] = new Track(
                (int) $row['TrackId'],
                $row['Name'],
                $album,
                $mediaType,
                $genre,
                $row['Composer'],
                (int) $row['Milliseconds'],
                (float) $row['Bytes'],
                (float) $row['UnitPrice']
            );
        }
        return $tracks;
    }

    public function update(
        int $id,
        TrackRequest $request
    ): bool {

        $sql =  <<<SQL
            UPDATE {$this->table}
            SET Name = :trackName, AlbumId = :albumId, MediaTypeId = :mediaTypeId, GenreId = :genreId, Composer = :composer, Milliseconds = :milliseconds, Bytes = :bytes, UnitPrice = :unitPrice
            WHERE TrackId = :trackId
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':trackName', $request->name, PDO::PARAM_STR);
        $stmt->bindParam(':albumId', $request->albumId, PDO::PARAM_INT);
        $stmt->bindParam(':mediaTypeId', $request->mediaTypeId, PDO::PARAM_INT);
        $stmt->bindParam(':genreId', $request->genreId, PDO::PARAM_INT);
        $stmt->bindParam(':composer', $request->composer, PDO::PARAM_STR);
        $stmt->bindParam(':milliseconds', $request->milliseconds, PDO::PARAM_STR);
        $stmt->bindParam(':bytes', $request->bytes, PDO::PARAM_INT);
        $stmt->bindParam(':unitPrice', $request->unitPrice, PDO::PARAM_STR);
        $stmt->bindParam(':trackId', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function create(TrackRequest $request): Track
    {

        $sql = <<<SQL
            SELECT MAX(TrackId) AS max_id FROM {$this->table}
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $maxId = (int) $stmt->fetchColumn();
        $nextId = $maxId + 1;

        $sql = <<<SQL
            INSERT INTO {$this->table}
            (trackId, name, mediaTypeId, genreId, composer, milliseconds, bytes, unitPrice)
            VALUES (:trackId, :trackName, :mediaTypeId, :genreId, :composer, :milliseconds, :bytes, :unitPrice)
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":trackId", $nextId, PDO::PARAM_INT);
        $stmt->bindParam(":trackName", $request->name, PDO::PARAM_STR);
        $stmt->bindParam(":mediaTypeId", $request->mediaTypeId, PDO::PARAM_INT);
        $stmt->bindParam(":genreId", $request->genreId, PDO::PARAM_INT);
        $stmt->bindParam(":composer", $request->composer, PDO::PARAM_INT);
        $stmt->bindParam(":milliseconds", $request->milliseconds, PDO::PARAM_INT);
        $stmt->bindParam(":bytes", $request->bytes, PDO::PARAM_INT);
        $stmt->bindParam(":unitPrice", $request->unitPrice, PDO::PARAM_INT);

        $stmt->execute();

        $trackId = (int) $this->conn->lastInsertId();

        $album = $this->albumRepo->getById((int) $request->albumId);
        $genre = $this->genreRepo->getById((int) $request->genreId);
        $mediaType = $this->mediaTypeRepo->getById((int) $request->mediaTypeId);

        return new Track(
            $trackId,
            $request->name,
            $album,
            $mediaType,
            $genre,
            $request->composer,
            $request->milliseconds,
            $request->bytes,
            $request->unitPrice
        );
    }

    public function search(string $searchString)
    {
        $sql = <<<SQL
            SELECT * FROM {$this->table} WHERE Track.Name LIKE :searchString
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":searchString", $searchString, PDO::PARAM_STR);
        $stmt->execute();

        $tracks = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $album = $this->albumRepo->getById($row['AlbumId']);
            $genre = $this->genreRepo->getById($row['GenreId']);
            $mediaType = $this->mediaTypeRepo->getById($row['MediaTypeId']);


            $tracks[] = new Track(
                $row['TrackId'],
                $row['Name'],
                $album,
                $mediaType,
                $genre,
                $row['Composer'],
                $row['Milliseconds'],
                $row['Bytes'],
                $row['UnitPrice']
            );
        }

        return $tracks;
    }

    public function getByComposer(string $composer)
    {
        $sql = <<<SQL
            SELECT * FROM Track WHERE Composer = :composer
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':composer', $composer, PDO::PARAM_STR);
        $stmt->execute();

        $tracks = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $album = $this->albumRepo->getById($row['AlbumId']);
            $genre = $this->genreRepo->getById($row['GenreId']);
            $mediaType = $this->mediaTypeRepo->getById($row['MediaTypeId']);

            $tracks[] = new Track(
                $row['TrackId'],
                $row['Name'],
                $album,
                $mediaType,
                $genre,
                $row['Composer'],
                $row['Milliseconds'],
                $row['Bytes'],
                $row['UnitPrice']
            );
        }
        return $tracks;
    }

    public function delete(int $id) {
        $sql = <<<SQL
            DELETE FROM {$this->table} WHERE TrackId = :trackId
        SQL;

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':trackId', $id, PDO::PARAM_INT);
        $stmt->execute();

        return true;
    }
}