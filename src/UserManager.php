<?php

namespace Isidora\SongContest;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class UserManager
{
    private $usersFile = __DIR__ . '/../data/users.json';
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger('user_actions');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::DEBUG));
    }

    public function register($username, $password)
    {
        $users = $this->loadUsers();

        if (isset($users[$username])) {
            $this->logger->warning("Pokušaj registracije već postojećeg korisnika: $username");
            return "Korisnik već postoji!";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $users[$username] = [
            'password' => $hashedPassword
        ];
        $this->saveUsers($users);

        $this->logger->info("Korisnik uspešno registrovan: $username");

        return "Registracija uspesna!";
    }

    public function login($username, $password)
    {

        $users = $this->loadUsers();

        if (!isset($users[$username])) {
            $this->logger->error("Pokušaj prijave sa nepostojećim korisničkim imenom: $username");
            return "Korisnicko ime nije pronadjeno";
        }

        if (password_verify($password, $users[$username]['password'])) {
            session_start();
            $_SESSION['username'] = $username;
            $this->logger->info("Korisnik uspešno prijavljen: $username");
            return "Prijava uspešna!";
        } else {
            $this->logger->error("Neuspešna prijava za korisnika: $username - neispravna lozinka");
            return "Neispravna lozinka!";
        }
    }

    public function loadUsers()
    {
        if (!file_exists($this->usersFile)) {
            return [];
        }
        $json = file_get_contents($this->usersFile);
        return json_decode($json, true);
    }

    private function saveUsers($users)
    {
        file_put_contents($this->usersFile, json_encode($users, JSON_PRETTY_PRINT));
    }
}
