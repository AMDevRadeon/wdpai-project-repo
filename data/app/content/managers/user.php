<?php

class User {
    private $name;
    private $email;
    private $passhash;

    public function __construct($email, $name, $passhash)
    {
        $this->name = $name;
        $this->email = $email;
        $this->passhash = $passhash;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPasshash()
    {
        return $this->passhash;
    }
}