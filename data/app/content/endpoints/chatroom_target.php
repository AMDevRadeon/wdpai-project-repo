<?php

include_once __DIR__ . "/../managers/database_control_manager.php";


$content_type = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if ($content_type === "application/json") {
    session_start();
    $content = trim(file_get_contents("php://input"));
    $decoded = json_decode($content, true);

    if (is_array($decoded)) {
        if (isset($_SESSION['user-name']) && isset($_SESSION['user-email']) && isset($_SESSION['user-is_admin']) && $_SESSION['user-is_admin'] === true) {
            if ($decoded['reason'] === "get_chatrooms") {
                $message_manager = new AdminMessageManager();
                $data = $message_manager->fetchChatrooms();
                $encoded = json_encode($data);    
                print $encoded;
            }
            else {
                print json_encode(['status' => 0, 'messages' => "Malformed request - wrong reason"]);
            }
        }
        else if (isset($_SESSION['user-name']) && isset($_SESSION['user-email']) && isset($_SESSION['user-is_admin']) && $_SESSION['user-is_admin'] === false) {
            if ($decoded['reason'] === "get_chatrooms") {
                $message_manager = new ChatroomManager();
                $data = $message_manager->fetchPrivateChatrooms();
                $encoded = json_encode($data);    
                print $encoded;
            }
            else {
                print json_encode(['status' => 0, 'messages' => "Malformed request - wrong reason"]);
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