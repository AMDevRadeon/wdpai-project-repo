<?php

include_once __DIR__ . "/../managers/database_control_manager.php";


$content_type = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if ($content_type === "application/json") {
    session_start();
    $content = trim(file_get_contents("php://input"));
    $decoded = json_decode($content, true);

    if (is_array($decoded)) {
        if (isset($_SESSION['user-name']) && isset($_SESSION['user-email']) && isset($_SESSION['user-dbid'])) {
            if (isset($decoded["date_sent"]) && isset($decoded["message"])) {
                if (mb_strlen($decoded["message"]) > 2000) {
                    print json_encode(['status' => 0, 'messages' => "Message too long"]);
                    exit();
                }

                if (mb_strlen($decoded["message"]) == 0) {
                    print json_encode(['status' => 0, 'messages' => "Message too short"]);
                    exit();
                }

                $message_manager = new MessageManager();
                if ($decoded['request'] === "global") {
                    $response = $message_manager->sendGlobalChatroomMessages($decoded["date_sent"], $_SESSION['user-dbid'], $decoded["message"]);
                    $encoded = json_encode($data);
                    print $encoded;
                }
                else if ($decoded['request'] === "private" && isset($decoded['room_id'])) {
                    $response = $message_manager->sendPrivateChatroomMessages($decoded['room_id'], $decoded["date_sent"], $_SESSION['user-dbid'], $decoded["message"]);
                    $encoded = json_encode($data);
                    print $encoded;
                }
                else {
                    print json_encode(['status' => 0, 'messages' => "Malformed request (3)"]);
                }
            }
            else {
                print json_encode(['status' => 0, 'messages' => "Malformed request (2)"]);
            }
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