<?php

require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Controllers/AlbumController.php';
require_once __DIR__ .'/../app/Services/AlbumService.php';

require_once __DIR__ . '/../app/Controllers/ArtistController.php';
require_once __DIR__ . '/../app/Services/ArtistService.php';

require_once __DIR__ . '/../app/Controllers/GenreController.php';
require_once __DIR__ . '/../app/Services/GenreService.php';

require_once __DIR__ . '/../app/Controllers/MediaTypeController.php';
require_once __DIR__ . '/../app/Services/MediaTypeService.php';

require_once __DIR__ . '/../app/Controllers/PlaylistController.php';
require_once __DIR__ . '/../app/Services/PlaylistService.php';

require_once __DIR__ . '/../app/Controllers/PlaylistTrackController.php';
require_once __DIR__ . '/../app/Services/PlaylistTrackService.php';

require_once __DIR__ . '/../app/Controllers/TrackController.php';
require_once __DIR__ . '/../app/Services/TrackService.php';

use App\Controllers\AlbumController;
use App\Controllers\ArtistController;
use App\Controllers\GenreController;
use App\Controllers\MediaTypeController;
use App\Controllers\PlaylistController;
use App\Controllers\PlaylistTrackController;
use App\Controllers\TrackController;
use App\Config\Database;

$db = Database::getConnection();

$albumController = new AlbumController($db);
$artistController = new ArtistController($db);
$genreController = new GenreController($db);
$mediaTypeController = new MediaTypeController($db);
$playlistController = new PlaylistController($db);
$playlistTrackController = new PlaylistTrackController($db);
$trackController = new TrackController($db);

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

echo "Current URI: ". $requestUri . "\n";
echo "Request method: ", $requestMethod . "\n";

$normalizedUri = rtrim($requestUri, '/');

## Front page
if ($requestMethod == "GET" && $requestUri == '/phpExam/') {
    include __DIR__ . "/../index.php";
}

#Artists
if ($requestMethod == 'GET' && $normalizedUri == '/phpExam/artists/create'){
    include __DIR__ . "/../views/artists/create.php";
}  elseif ($requestMethod == 'POST' && $normalizedUri == '/phpExam/artists'){
    $artistController->store();
} elseif (preg_match('/phpExam\/artists$/', $normalizedUri) && isset($_GET['s'])) {
    $searchParam = '%' . $_GET['s'] . '%';
    $artistController->search($searchParam);
} elseif ($requestMethod == 'GET' && preg_match('/phpExam\/artists\/(\d+)\/albums/', $normalizedUri, $matches)) {
    $albumController->getAlbumsByArtistId($matches[1]);
} elseif ($requestMethod == 'GET' && $normalizedUri == '/phpExam/artists') {
    $artistController->index();
} elseif ($requestMethod == 'GET' && preg_match('/phpExam\/artists\/(\d+)/', $normalizedUri, $matches)) {
    $artistController->show($matches[1]);
} elseif ($requestMethod == 'DELETE' && preg_match('/phpExam\/artists\/(\d+)/', $normalizedUri, $matches)) {
    $artistController->delete($matches[1]);
}

#Albums

if (preg_match('/phpExam\/albums$/', $normalizedUri) && isset($_GET['s'])) {
    $searchParam = '%' . $_GET['s'] . '%';
    $albumController->search($searchParam);
} elseif($requestMethod == 'GET' &&  $normalizedUri == '/phpExam/albums') {
    $albumController->index();
} elseif ($requestMethod == 'GET' && $normalizedUri == '/phpExam/albums/create'){
    include __DIR__ . "/../views/albums/create.php";
} elseif (preg_match('/phpExam\/albums\/(\d+)/', $normalizedUri, $matches)) {
    echo 'TEST';
    $albumController->show($matches[1]);
}

#Genres
if($requestMethod == 'GET' && $normalizedUri == '/phpExam/genres'){
    $genreController->index();
} elseif ($requestMethod == 'GET' && preg_match('/phpExam\/genres\/(\d+)/', $normalizedUri, $matches)) {
    $genreController->show($matches[1]);
}

#MediaTypes
if($requestMethod == 'GET' && $normalizedUri == '/phpExam/mediatypes'){
    $mediaTypeController->index();
} elseif (preg_match('/phpExam\/mediatypes\/(\d+)/', $normalizedUri, $matches)) {
    $mediaTypeController->show($matches[1]);
}

#Playlist
if($requestMethod == 'GET' && $normalizedUri == '/phpExam/playlists'){
    $playlistController->index();
} elseif (preg_match('/phpExam\/playlists\/(\d+)/', $normalizedUri, $matches)) {
    $mediaTypeController->show($matches[1]);
}

#PlaylistTrack
if ($requestMethod == 'GET' && $normalizedUri == '/phpExam/playlisttracks'){
    $playlistTrackController->index();
} elseif ($requestMethod == 'GET' && preg_match('#^/phpExam/playlisttracks/(\d+)/(\d+)$#', $normalizedUri, $matches)) {
    $playlistTrackController->show($matches[1], $matches[2]);
}
class PlaylistResponse {
    public int $playlistId;
    public string $name;
}