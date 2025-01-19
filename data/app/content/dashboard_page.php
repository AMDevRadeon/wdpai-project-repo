<?php

include_once __DIR__ . "/router_manager.php";

session_start();

if (!isset($_SESSION['user-name']) || !isset($_SESSION['user-email'])) {
    Routes::redirect_to('/login');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="static/css/dashboard_page.css">
    <script>
        let self_name = "<?php echo $_SESSION['user-name'];?>";
        let self_email = "<?php echo $_SESSION['user-email'];?>";
    </script>
    <script defer type="text/javascript" src="static/js/dashboard.js"></script>
    <title>WeChat - <?php echo $_SESSION['user-name']?></title>
</head>
<body>
    <div id="menubar-left">
        <button id="logout">Log out</button>
    </div>
    <div id="main">
        <div id="main-left">
            <div id="contacts-list"></div>
            <div id="user-info">
                <div id="user-info-container">
                    <p><?php echo $_SESSION['user-name']?></p>
                    <p><?php echo $_SESSION['user-email']?></p>
                </div>
            </div>
        </div>
        <div id="main-right">
            <div id="message-place"></div>
            <div id="message-form">
                <textarea name="input-message" id="message-input"></textarea>
                <button id="message-button">Send</button>
            </div>
        </div>
    </div>
</body>
</html>