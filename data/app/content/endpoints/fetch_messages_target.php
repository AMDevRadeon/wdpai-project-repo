<?php

include_once __DIR__ . "/../managers/database_control_manager.php";


$content_type = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if ($content_type === "application/json") {
    session_start();
    $content = trim(file_get_contents("php://input"));
    $decoded = json_decode($content, true);

    if (is_array($decoded)) {
        if (isset($_SESSION['user-name']) && isset($_SESSION['user-email'])) {
            $message_manager = new MessageManager();
            $from_when = $decoded["time"];
            $data = $message_manager->fetchGlobalChatroomMessages($from_when);
            $encoded = json_encode($data);
            print $encoded;
        }
        else {
            print json_encode(['status' => 0, 'messages' => "No credentials"]);
        }
    }
    else {
        print json_encode(['status' => 0, 'messages' => "Malformed request"]);
    }
}
else {
    http_response_code(404);
    ob_start();
    include __DIR__ . self::$content_path . "404.php";
    print ob_get_clean();
    exit();
}

exit();