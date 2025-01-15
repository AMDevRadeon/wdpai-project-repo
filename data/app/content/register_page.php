<?php
require_once __DIR__ . "/managers/login_manager.php";

session_start();

$register_handle = new RegisterManager();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="static/css/register_page.css">
    <title>WeChat - Registration</title>
</head>
<body>
    <form action="" method="post">
        <label for="input-email">Your email:</label>
        <input type="email" name="input-email" id="input-email">
        <label for="input-name">Your name:</label>
        <input type="text" name="input-name" id="input-name">
        <label for="input-password">Your password:</label>
        <input type="password" name="input-password" id="input-password">
        <input type="submit" value="Register now">
        <a href="login">Already got one? Try logging in!</a>
        <div id=errors>
            <?php
                print($register_handle());
            ?>
        </div>
    </form>
</body>
</html>

