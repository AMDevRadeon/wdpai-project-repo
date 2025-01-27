<?php

include_once __DIR__ . "/../managers/database_control_manager.php";


$content_type = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if ($content_type === "application/json") {
    session_start();
    $content = trim(file_get_contents("php://input"));
    $decoded = json_decode($content, true);

    if (is_array($decoded)) {
        if (isset($_SESSION['user-name']) && isset($_SESSION['user-email']) && isset($_SESSION['user-is_admin']) && $_SESSION['user-is_admin'] === true) {
            $message_manager = new AdminMessageManager();
            if ($decoded['reason'] === "get_chatrooms") {
                $data = $message_manager->fetchChatrooms();
                $encoded = json_encode($data);    
                print $encoded;
            }
            else if ($decoded['reason'] === "get_users") {
                $data = $message_manager->fetchUsers();
                $encoded = json_encode($data);    
                print $encoded;
            }
            else if ($decoded['reason'] === "add_chatrooms" && isset($decoded['name'])) {
                $data = $message_manager->addChatrooms($decoded['name']);
                $encoded = json_encode($data);    
                print $encoded;
            }
            else if ($decoded['reason'] === "del_chatrooms" && isset($decoded['chatroom'])) {
                $data = $message_manager->deleteChatrooms($decoded['chatroom']);
                $encoded = json_encode($data);    
                print $encoded;
            }
            else if ($decoded['reason'] === "add_users" && isset($decoded['chatroom']) && isset($decoded['user'])) {
                $data = $message_manager->addUserToChatroom($decoded['user'], $decoded['chatroom']);
                $encoded = json_encode($data);    
                print $encoded;
            }
            else if ($decoded['reason'] === "del_users" && isset($decoded['chatroom']) && isset($decoded['user'])) {
                $data = $message_manager->deleteUserFromChatroom($decoded['user'], $decoded['chatroom']);
                $encoded = json_encode($data);    
                print $encoded;
            }
            else {
                print json_encode(['status' => 0, 'messages' => "Malformed request - wrong reason", 'request' => $decoded]);
            }
        }
        else if (isset($_SESSION['user-name']) && isset($_SESSION['user-email']) && isset($_SESSION['user-is_admin']) && $_SESSION['user-is_admin'] === false) {
            if ($decoded['reason'] === "get_chatrooms") {
                $message_manager = new ChatroomManager();
                $data = $message_manager->fetchPrivateChatrooms($_SESSION['user-dbid']);
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