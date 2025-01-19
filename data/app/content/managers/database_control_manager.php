<?php

require_once __DIR__ . "/database_manager.php";
require_once __DIR__ . "/user.php";

class UserManager
{
    protected $database;
    const GETUSERBYVALUE_QUERY = 'SELECT id, name, email, passhash FROM user_data WHERE %s = :value';
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
        $query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'User', array(NULL, NULL, NULL, NULL));

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
        QUERY;

    const GETMESSAGESGLOBALCHAT_FROM_TIMESTAMP_QUERY = <<<QUERY
        SELECT user_data.name, global_chat.sent_date, global_chat.message FROM global_chat
        LEFT OUTER JOIN user_data ON global_chat.user_id = user_data.id
        WHERE global_chat.sent_date > :timestamp::timestamp
        QUERY;
    
    const SENDMESSAGEGLOBALCHAT_QUERY = <<<QUERY
        INSERT INTO global_chat(sent_date, user_id, message) VALUES (:date, :id, :message)
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
}