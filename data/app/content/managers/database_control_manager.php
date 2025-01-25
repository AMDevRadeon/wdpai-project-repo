<?php

require_once __DIR__ . "/database_manager.php";
require_once __DIR__ . "/user.php";

class UserManager
{
    protected $database;
    const GETUSERBYVALUE_QUERY = 'SELECT id, name, email, passhash FROM user_data WHERE %s = :value';

    const GETUSERBYAVLUEWITHADMINCRED_QUERY = <<<QUERY
        SELECT user_data.id, name, email, passhash, is_admin FROM user_data
        JOIN user_admin ON user_admin.id = user_data.id
        WHERE %s = :value
    QUERY;

    const PUSHUSER_QUERY = 'INSERT INTO user_data(name, email, passhash) VALUES (:name, :email, :passhash)';
    
    public function __construct()
    {
        $this->database = new DatabaseManager();
    }

    public function getUserByValue(string $type, string $value)
    {
        $query = ($this->database)()->prepare(sprintf(self::GETUSERBYVALUE_QUERY, $type));
        $query->bindParam(':value', $value, PDO::PARAM_STR);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'User', array(NULL, NULL, NULL, NULL, NULL));

        $user = $query->fetch();

        if (!$user) {
            return null;
        }
        else {
            return $user;
        } 
    }

    public function getUserByValueWithAdmin(string $type, string $value)
    {
        $query = ($this->database)()->prepare(sprintf(self::GETUSERBYAVLUEWITHADMINCRED_QUERY, $type));
        $query->bindParam(':value', $value, PDO::PARAM_STR);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'User', array(NULL, NULL, NULL, NULL, NULL));

        $user = $query->fetch();

        if (!$user) {
            return null;
        }
        else {
            return $user;
        } 
    }

    public function pushUser(User $user)
    {
        $query = ($this->database)()->prepare(self::PUSHUSER_QUERY);
        $query->bindParam(':name', $user->getName(), PDO::PARAM_STR);
        $query->bindParam(':email', $user->getEmail(), PDO::PARAM_STR);
        $query->bindParam(':passhash', $user->getPasshash(), PDO::PARAM_STR);
        return $query->execute();
    }
}

class MessageManager
{
    protected $database;
    const GETMESSAGESGLOBALCHAT_ALL_QUERY = <<<QUERY
        SELECT user_data.name, global_chat.sent_date, global_chat.message FROM global_chat
        LEFT OUTER JOIN user_data ON global_chat.user_id = user_data.id
        ORDER BY global_chat.sent_date
        QUERY;

    const GETMESSAGESGLOBALCHAT_FROM_TIMESTAMP_QUERY = <<<QUERY
        SELECT user_data.name, global_chat.sent_date, global_chat.message FROM global_chat
        LEFT OUTER JOIN user_data ON global_chat.user_id = user_data.id
        WHERE global_chat.sent_date > :timestamp::timestamp
        ORDER BY global_chat.sent_date
        QUERY;
    
    const SENDMESSAGEGLOBALCHAT_QUERY = <<<QUERY
        INSERT INTO global_chat(sent_date, user_id, message) VALUES (:date, :id, :message)
        QUERY;


    const GETMESSAGESPRIVATECHAT_ALL_QUERY = <<<QUERY
        SELECT user_data.name, private_chat.sent_date, private_chat.message FROM private_chat
        LEFT OUTER JOIN user_data ON private_chat.user_id = user_data.id
        WHERE private_chat.conversation_id = :conv_id
        ORDER BY private_chat.sent_date
        QUERY;

    const GETMESSAGESPRIVATECHAT_FROM_TIMESTAMP_QUERY = <<<QUERY
        SELECT user_data.name, private_chat.sent_date, private_chat.message FROM private_chat
        LEFT OUTER JOIN user_data ON private_chat.user_id = user_data.id
        WHERE (private_chat.conversation_id = :conv_id AND private_chat.sent_date > :timestamp::timestamp)
        ORDER BY private_chat.sent_date
        QUERY;

    const SENDMESSAGEPRIVATECHAT_QUERY = <<<QUERY
        INSERT INTO private_chat(conversation_id, sent_date, user_id, message) VALUES (:convo, :date, :id, :message)
        QUERY;
    
    public function __construct()
    {
        $this->database = new DatabaseManager();
    }

    public function fetchGlobalChatroomMessages($from_timestamp)
    {
        $query = NULL;

        if ($from_timestamp === NULL) {
            $query = ($this->database)()->prepare(self::GETMESSAGESGLOBALCHAT_ALL_QUERY);        
        }
        else {
            $query = ($this->database)()->prepare(self::GETMESSAGESGLOBALCHAT_FROM_TIMESTAMP_QUERY);
            $query->bindParam(':timestamp', $from_timestamp, PDO::PARAM_STR);    
        }
        $status = $query->execute();

        return ['status' => $status, 'messages' => $query->fetchAll()];
    }

    public function sendGlobalChatroomMessages($date_sent, $id, $message)
    {
        $query = ($this->database)()->prepare(self::SENDMESSAGEGLOBALCHAT_QUERY);
        $query->bindParam(':date', $date_sent, PDO::PARAM_STR);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->bindParam(':message', $message, PDO::PARAM_STR);
        
        return $query->execute();
    }

    public function fetchPrivateChatroomMessages($id_chat, $from_timestamp)
    {
        $query = NULL;

        // CHECK ID CHAT

        if ($from_timestamp === NULL) {
            $query = ($this->database)()->prepare(self::GETMESSAGESPRIVATECHAT_ALL_QUERY);
            $query->bindParam(':conv_id', $id_chat, PDO::PARAM_INT);        
        }
        else {
            $query = ($this->database)()->prepare(self::GETMESSAGESPRIVATECHAT_FROM_TIMESTAMP_QUERY);
            $query->bindParam(':conv_id', $id_chat, PDO::PARAM_INT);
            $query->bindParam(':timestamp', $from_timestamp, PDO::PARAM_STR);    
        }
        $status = $query->execute();

        return ['status' => $status, 'messages' => $query->fetchAll()];
    }

    public function sendPrivateChatroomMessages($convo, $date_sent, $id, $message)
    {
        $query = ($this->database)()->prepare(self::SENDMESSAGEPRIVATECHAT_QUERY);
        $query->bindParam(':convo', $convo, PDO::PARAM_INT);
        $query->bindParam(':date', $date_sent, PDO::PARAM_STR);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->bindParam(':message', $message, PDO::PARAM_STR);
        
        return $query->execute();
    }

}


class ChatroomManager
{
    protected $database;

    const GETCHATROOMS_PRIVATE_QUERY = <<<QUERY
    SELECT chat_connections.name, chat_connections.id, user_data.name FROM chat_connections
    JOIN (
        SELECT * FROM private_chat_connection
        WHERE private_chat_connection.conversation_id IN (
            SELECT private_chat_connection.conversation_id FROM private_chat_connection 
            WHERE private_chat_connection.user_id = :value)) private_convos
    ON chat_connections.id = private_convos.conversation_id
    JOIN user_data ON private_convos.user_id = user_data.id
    WHERE chat_connections.is_deleted = false
    QUERY;

    public function __construct()
    {
        $this->database = new DatabaseManager();
    }

    public function fetchPrivateChatrooms($id)
    {
        $query = ($this->database)()->prepare(self::GETCHATROOMS_PRIVATE_QUERY);
        $query->bindParam(':value', $id, PDO::PARAM_INT);    
        $status = $query->execute();

        return ['status' => $status, 'chatrooms' => $query->fetchAll()];
    }
}




class AdminMessageManager
{
    protected $database;

    const GETCHATROOMS_ALL_QUERY = <<<QUERY
        SELECT chat_connections.name, chat_connections.id, user_data.name FROM chat_connections
        LEFT OUTER JOIN private_chat_connection ON chat_connections.id = private_chat_connection.conversation_id
        LEFT OUTER JOIN user_data ON private_chat_connection.user_id = user_data.id
        WHERE chat_connections.is_deleted = false
        QUERY;

    const ADDCHATROOM = <<<QUERY
        INSERT INTO chat_connections(name, is_deleted) VALUES (:name, false)
        QUERY;

    const REMOVECHATROOM = <<<QUERY
        UPDATE chat_connections SET is_deleted = true
        WHERE id = :convo
        QUERY;
    
    const GETCHATROOMIDS = <<<QUERY
        SELECT id FROM chat_connections
        QUERY;

    const GETUSERLIST = <<<QUERY
        SELECT * FROM user_data_name_view
        QUERY;

    const ADDUSERIDTOCHATROOM = <<<QUERY
        INSERT INTO private_chat_connection(conversation_id, user_id) VALUES (:convo, :user)
        QUERY;

    const REMOVEUSERFROMCHATROOM = <<<QUERY
        DELETE FROM private_chat_connection WHERE conversation_id = :convo AND user_id = :user
        QUERY;

    private function array_unique_flatten(array $array) {
        $return = array();
        array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
        return array_unique($return);
    }

    public function __construct()
    {
        $this->database = new DatabaseManager();
    }

    public function fetchChatrooms()
    {
        $query = ($this->database)()->prepare(self::GETCHATROOMS_ALL_QUERY);        

        $status = $query->execute();

        return ['status' => $status, 'chatrooms' => $query->fetchAll()];
    }

    public function addChatrooms($name)
    {
        if ($name == null) {
            $name = 'Private chat';
        }

        $query = ($this->database)()->prepare(self::ADDCHATROOM);
        $query->bindParam(':name', $name, PDO::PARAM_STR);      

        return $query->execute();
    }

    public function deleteChatrooms($chat_id)
    {
        $query = ($this->database)()->prepare(self::REMOVECHATROOM); 
        
        $query->bindParam(':convo', $chat_id, PDO::PARAM_INT);
        $query->execute();

        return $query->rowCount();
    }

    public function addUserToChatroom($added_user_name, $chat_id)
    {
        $query = ($this->database)()->prepare(self::GETUSERLIST);
        $query->execute();

        $list_user_names = $this->array_unique_flatten($query->fetchAll());

        $query = ($this->database)()->prepare(self::GETCHATROOMIDS);
        $query->execute();

        $list_chatroom_ids = $this->array_unique_flatten($query->fetchAll());

        if (!in_array($added_user_name, $list_user_names)) {
            return ['status' => false, 'message' => "no username"];
        }

        if (!in_array($chat_id, $list_chatroom_ids)) {
            return ['status' => false, 'message' => "no chatroom exists"];
        }

        if (in_array($added_user_name, $list_user_names) && in_array($chat_id, $list_chatroom_ids)) {
            $usermanager = new UserManager();
            $that_user = $usermanager->getUserByValue("name", $added_user_name);

            $query = ($this->database)()->prepare(self::ADDUSERIDTOCHATROOM);
            $query->bindParam(':convo', $chat_id, PDO::PARAM_INT);   
            $query->bindParam(':user', $that_user->getId(), PDO::PARAM_INT);
            
            return ['status' => $query->execute(), 'message' => "tried adding user to chat"];
        }
        else {
            return ['status' => false, 'message' => "something very weird"];
        }
    }


    public function deleteUserFromChatroom($removed_user_name, $chat_id)
    {
        $usermanager = new UserManager();
        $that_user = $usermanager->getUserByValue("name", $removed_user_name);

        if ($that_user === null) {
            return ['status' => false, 'message' => "no user exists"];
        }

        $query = ($this->database)()->prepare(self::REMOVEUSERFROMCHATROOM);
        $query->bindParam(':convo', $chat_id, PDO::PARAM_INT);   
        $query->bindParam(':user', $that_user->getId(), PDO::PARAM_INT);
        $query->execute();
        
        return ['status' => $query->rowCount(), 'message' => "tried deleting user from chat"];
    }
}