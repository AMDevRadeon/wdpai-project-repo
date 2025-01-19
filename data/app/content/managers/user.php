<?php

class User {
    private $id;
    private $name;
    private $email;
    private $passhash;
    private $is_admin;

    public function __construct($id, $email, $name, $passhash, $is_admin)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->passhash = $passhash;
        $this->is_admin = $is_admin;
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

    public function isAdmin()
    {
        return $this->is_admin;
    }
}