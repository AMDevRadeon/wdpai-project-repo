<?php

class Routes
{
    public static $routes_map;
    public static $content_path = "/../";

    public static function set_route($uri, $handler)
    {
        self::$routes_map[rtrim($uri, "/")] = $handler;
    }

    public static function route_to($uri)
    {
        if (!array_key_exists(rtrim($uri, "/"), self::$routes_map)) {
            http_response_code(404);
            ob_start();
            include __DIR__ . self::$content_path . "404.php";
            print ob_get_clean();
            exit();
        }
        else {
            http_response_code(200);
            ob_start();
            include __DIR__ . self::$content_path . self::$routes_map[rtrim($uri, "/")] . ".php";
            print ob_get_clean();
            exit();
        }
    }

    public static function redirect_to($uri)
    {
        $root = 'http://' . $_SERVER['HTTP_HOST'];
        header('Location: ' . $root . $uri);
        exit();
    }
}
