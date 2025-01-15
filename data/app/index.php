<?php

class Routes
{
    public static $routes_map;
    public static $content_path = "/content/";

    public static function set_route($uri, $handler)
    {
        self::$routes_map[rtrim($uri, "/")] = $handler;
    }

    public static function route_to($uri)
    {
        if (!array_key_exists(rtrim($uri, "/"), self::$routes_map)) {
            http_response_code(404);
            require __DIR__ . self::$content_path . "404.php";
        }
        else {
            http_response_code(200);
            ob_start();
            require __DIR__ . self::$content_path . self::$routes_map[rtrim($uri, "/")] . ".php";
            print ob_get_clean();
        }
    }
}

$request = $_SERVER['REQUEST_URI'];

Routes::set_route("", "landing_page");
Routes::set_route("register", "register_page");

Routes::route_to($request);

?>