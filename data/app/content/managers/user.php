<?php

class User {
    private $id;
    private $name;
    private $email;
    private $passhash;

    public function __construct($id, $email, $name, $passhash)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->passhash = $passhash;
    }

    public function getId()
    {
        return $this->id;
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