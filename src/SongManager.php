<?php

namespace Isidora\SongContest;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SongManager
{
    private $songsFile = __DIR__ . '/../data/songs.json';
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger('song_actions');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::DEBUG));
    }

    public function submitSong($title, $artist)
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            $this->logger->warning("Pokušaj unosa pesme bez prijave");
            return "Morate biti prijavljeni da biste uneli pesmu!";
        }

        $songs = $this->loadSongs();

        $songs[] = [
            'title' => $title,
            'artist' => $artist,
            'submitted_by' => $_SESSION['username']
        ];

        $this->saveSongs($songs);

        $this->logger->info("Pesma uspešno uneta od strane korisnika: " . $_SESSION['username'] . " - Pesma: $title, Izvođač: $artist");

        return "Pesma uspešno uneta!";
    }

    private function loadSongs()
    {
        if (!file_exists($this->songsFile)) {
            return [];
        }
        $json = file_get_contents($this->songsFile);
        return json_decode($json, true);
    }

    private function saveSongs($songs)
    {
        file_put_contents($this->songsFile, json_encode($songs, JSON_PRETTY_PRINT));
    }

    public function getAllSongs()
    {
        if (!file_exists($this->songsFile)) {
            return [];
        }

        $json = file_get_contents($this->songsFile);
        $songs = json_decode($json, true);

        return $songs ? $songs : [];
    }
}
