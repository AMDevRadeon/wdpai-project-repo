<?php

require_once __DIR__ . "/router_manager.php";
require_once __DIR__ . "/database_control_manager.php";

class RegisterManager {
    private $db_manager;
    private $is_valid_data;
    private $error_mesg;
    
    public function __construct()
    {
        $this->db_manager = NULL;
        $this->is_valid_data = true;
        $this->errors = '';
    }

    public function __invoke()
    {
        if (isset($_POST['submit'])) {
            $this->db_manager = new UserManager();

            $POST_user_data = new User($_POST['input-email'], $_POST['input-name'], $_POST['input-password']);
            // var_dump($POST_user_data);

            if ($POST_user_data->getName() == ''){
                $this->is_valid_data = false;
                $this->errors = '
                    <div class="error_mesg">No name was provided</div>';
            }
            else if ($POST_user_data->getEmail() == ''){
                $this->is_valid_data = false;
                $this->errors = '
                    <div class="error_mesg">No email was provided</div>';
            }
            else if ($POST_user_data->getPasshash() == ''){
                $this->is_valid_data = false;
                $this->errors = '
                    <div class="error_mesg">No password was provided</div>';
            }
            else if (!filter_var($POST_user_data->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $this->is_valid_data = false;
                $this->errors = '
                    <div class="error_mesg">Invalid email address</div>';
            }
            else if (strlen($POST_user_data->getPasshash()) < 10) {
                $this->is_valid_data = false;
                $this->errors = '
                    <div class="error_mesg">Password must have length of at least 10</div>';
            }

            if (!$this->is_valid_data) {
                return $this->errors;
            }

            $check_database = $this->db_manager->getUserByValue('email', $POST_user_data->getEmail());

            if ($check_database && $check_database->getEmail() === $POST_user_data->getEmail()) {
                $this->is_valid_data = false;
                $this->errors = '
                    <div class="error_mesg">Email already used. Try using another one or try <a href="login">logging in</a></div>';
            }

            // var_dump($this->is_valid_data);
            if (!$this->is_valid_data) {
                return $this->errors;
            }

            $passhash = hash_hmac("sha256", $POST_user_data->getPasshash(), PASS_PEPPER);
            $passhash_prepared = password_hash($passhash, PASSWORD_ARGON2ID);
            $this->is_valid_data = $this->db_manager->pushUser(new User($POST_user_data->getEmail(), $POST_user_data->getName(), $passhash_prepared));

            if (!$this->is_valid_data) {
                $this->errors = '
                    <div class="error_mesg">Something went wrong with database, try again later</div>';
                return $this->errors;
            }
            else {
                $_SESSION['has_created_account_successfully'] = true;
                Routes::redirect_to('/login');
            }
        }
    }
}

class LoginManager {
    private $db_manager;
    private $is_valid_data;
    private $error_mesg;
    
    public function __construct()
    {
        $this->db_manager = NULL;
        $this->is_valid_data = true;
        $this->errors = '';
    }

    public function __invoke()
    {
        if (isset($_SESSION['user-name']) && isset($_SESSION['user-email'])) {
            Routes::redirect_to('/dashboard');
        }

        if (isset($_POST['submit'])) {
            if (isset($_SESSION['has_created_account_successfully']) && $_SESSION['has_created_account_successfully'] === true) {
                $this->errors = '
                <div class="error_mesg">Account successfully created, try logging in</div>';
                unset($_SESSION['has_created_account_successfully']);
                return $this->errors;
            }

            $this->db_manager = new UserManager();

            $POST_user_data = new User($_POST['input-email'], NULL, $_POST['input-password']);

            if ($POST_user_data->getPasshash() == ''){
                $this->is_valid_data = false;
                $this->errors = '
                    <div class="error_mesg">No password was provided</div>';
            }
            else if ($POST_user_data->getEmail() == ''){
                $this->is_valid_data = false;
                $this->errors = '
                    <div class="error_mesg">No email was provided</div>';
            }

            if (!$this->is_valid_data) {
                return $this->errors;
            }

            $check_database = $this->db_manager->getUserByValue('email', $POST_user_data->getEmail());

            if (!$check_database) {
                $this->is_valid_data = false;
                $this->errors = '
                    <div class="error_mesg">Invalid login information</div>';
                return $this->errors;
            }

            $passhash = hash_hmac("sha256", $POST_user_data->getPasshash(), PASS_PEPPER);


            if ($check_database && $check_database->getEmail() === $POST_user_data->getEmail() && password_verify($passhash, $check_database->getPasshash())) {
                //Login successful
                $_SESSION['user-email'] = $check_database->getEmail();
                $_SESSION['user-name'] = $check_database->getName();
                Routes::redirect_to('/dashboard');
            }
            else {
                $this->is_valid_data = false;
            }

            if (!$this->is_valid_data) {
                $this->errors = '
                    <div class="error_mesg">Invalid login information</div>';
                return $this->errors;
            }
        }
    }
}