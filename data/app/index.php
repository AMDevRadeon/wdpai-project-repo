<?php

include_once __DIR__ . "/content/managers/router_manager.php";

$request = $_SERVER['REQUEST_URI'];

Routes::$routes_map = array();

Routes::set_route("", "landing_page");
Routes::set_route("/register", "register_page");
Routes::set_route("/login", "login_page");

Routes::route_to($request);

?>