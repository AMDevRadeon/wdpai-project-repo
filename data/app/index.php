<?php

include_once __DIR__ . "/content/managers/router_manager.php";

$request = $_SERVER['REQUEST_URI'];

Routes::$routes_map = array();

Routes::set_route("", "landing_page");
Routes::set_route("/register", "register_page");
Routes::set_route("/login", "login_page");
Routes::set_route("/dashboard", "dashboard_page");
Routes::set_route("/logout", "managers/logout_manager");
Routes::set_route("/fetch_messages", "endpoints/fetch_messages_target");
Routes::set_route("/send_messages", "endpoints/send_messages_target");


Routes::route_to($request);

?>