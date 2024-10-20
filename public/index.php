<?php

require '../vendor/autoload.php';

use Isidora\SongContest\UserManager;
use Isidora\SongContest\SongManager;

session_start();

$userManager = new UserManager();
$songManager = new SongManager();


$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Rutiranje
if ($method === 'POST') {
    if ($uri === '/register') {
        $data = json_decode(file_get_contents('php://input'), true);
        echo $userManager->register($data['username'], $data['password']);
    } elseif ($uri === '/login') {
        $data = json_decode(file_get_contents('php://input'), true);
        echo $userManager->login($data['username'], $data['password']);
    } elseif ($uri === '/song') {
        $data = json_decode(file_get_contents('php://input'), true);
        echo $songManager->submitSong($data['title'], $data['artist']);
    }
} elseif ($method === 'GET' && $uri === '/songs') {
    $songs = $songManager->getAllSongs();
    echo json_encode($songs);
} elseif ($uri === '/index.php' || $uri === '/') {
    echo "Welcome to the Song Contest API!";
} else {
    echo 'No route found';
}
