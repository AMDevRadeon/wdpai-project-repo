<?php

require_once __DIR__ . "/database_manager.php";
require_once __DIR__ . "/user.php";

class UserManager
{
    protected $database;
    const GETUSERBYVALUE_QUERY = 'SELECT email, name, passhash FROM user_data WHERE %s = :value';
    const PUSHUSER_QUERY = 'INSERT INTO user_data(name, email, passhash) VALUES (:name, :email, :passhash)';
    
    public function __construct()
    {
        $this->database = new DatabaseManager();
    }

    public function getUserByValue(string $type, string $value)
    {
        $query = ($this->database)()->prepare(sprintf(self::GETUSERBYVALUE_QUERY, $type));
        $query->bindParam(':value', $value, PDO::PARAM_STR);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'User', array(NULL, NULL, NULL));

        $user = $query->fetch();

        if (!$user) {
            return null;
        }
        else {
            return $user;
        } 
    }

    public function pushUser(User $user)
    {
        $query = ($this->database)()->prepare(self::PUSHUSER_QUERY);
        $query->bindParam(':name', $user->getName(), PDO::PARAM_STR);
        $query->bindParam(':email', $user->getEmail(), PDO::PARAM_STR);
        $query->bindParam(':passhash', $user->getPasshash(), PDO::PARAM_STR);
        return $query->execute();
    }
}