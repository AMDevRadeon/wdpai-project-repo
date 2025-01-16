<?php

include_once __DIR__ . "/router_manager.php";

class LogoutManager
{
    public static function logout() {
        session_unset();
        session_destroy();
        Routes::redirect_to('');
    }
}

session_start();

LogoutManager::logout();

?>