<?php
require_once __DIR__ . "/managers/login_manager.php";

session_start();
$login_handle = new LoginManager();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="static/css/register_page.css">
    <title>WeChat - Login</title>
</head>
<body>
    <form action="" method="post">
        <label for="input-email">Your email:</label>
        <input type="email" name="input-email" id="input-email">
        <label for="input-password">Your password:</label>
        <input type="password" name="input-password" id="input-password">
        <input type="submit" name="submit" value="Login">
        <a href="register">Haven't got yourself an account? Register here</a>
        <div id=errors>
            <?php
                print($login_handle());
            ?>
        </div>
    </form>
</body>
</html>