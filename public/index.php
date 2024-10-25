<?php


require '../vendor/autoload.php';

use Isidora\SongContest\UserManager;
use Isidora\SongContest\SongManager;

session_start();

$userManager = new UserManager();
$songManager = new SongManager();

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

function handleInvalidJson($data)
{
    if (json_last_error() !== JSON_ERROR_NONE || !$data) {
        resp_json(['message' => 'Invalid JSON format'], 400);
    }
}

if ($method === 'POST') {
    if ($uri === '/logout') {
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            resp_json(['message' => 'Niste ulogovani'], 400);
        } else {
            $_SESSION = [];
            session_destroy();
            resp_json(['message' => 'Logout successful'], 200);
        }
    } else {
        $data = json_decode(file_get_contents('php://input'), true);
        handleInvalidJson($data);

        if ($uri === '/register') {
            resp_json($userManager->register($data['username'], $data['password']));
        } elseif ($uri === '/login') {
            resp_json($userManager->login($data['username'], $data['password']));
        } elseif ($uri === '/song') {
            resp_json($songManager->submitSong($data['title'], $data['artist']));
        }
    }
} elseif ($method === 'GET') {
    if ($uri === '/songs') {
        $songs = $songManager->getAllSongs();
        resp_json(['songs' => $songs]);
    } elseif ($uri === '/index.php' || $uri === '/') {
        resp_json(['message' => 'Welcome to the Song Contest API', 'version' => '1.0.0']);
    } else {
        resp_json(['message' => 'Not found'], 404);
    }
} else {
    resp_json(['message' => 'Method not allowed'], 405);
}
