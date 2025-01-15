<?php

require_once __DIR__ . "/../../keys.php";

class DatabaseManager
{
    private $server;
    private $database;
    private $user;
    private $password;

    public function __construct()
    {
        $this->server = POSTGRES_SERVER;
        $this->database = POSTGRES_DATABASE;
        $this->user = POSTGRES_USER;
        $this->password = POSTGRES_PASSWORD;
    }

    public function __invoke()
    {
        try {
            return new PDO("pgsql:host=$this->server;dbname=$this->database", $this->user, $this->password);
        } catch (PDOException $e) {
            die("Could not connect to the database '$this->server': " . $e->message());
        }
    }
}