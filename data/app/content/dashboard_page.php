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
    <?php
        if ($_SESSION['user-is_admin']) {
            echo "<script defer type=\"text/javascript\" src=\"static/js/dashboard_admin.js\"></script>";
        }
    ?>
    <script defer type="text/javascript" src="static/js/dashboard.js"></script>
    <title>WeChat - <?php echo $_SESSION['user-name']?></title>
</head>
<body>
    <div id="menubar-left">
        <img src="static/img/logo_wechat_transparent.png" alt="Logo" srcset="">
        <button id="logout">
            <img src="static/img/off_bttn.png" alt="Log off" srcset="">
        </button>
    </div>
    <div id="main">
        <div id="main-left">
            <div id="contacts-list">
                <div id="global-chat-contact" class="contacts-list-contact contacts-list-contact-enabled">
                    <p>Global chat</p>
                    <p class="person">Everyone</p>
                </div>

                <div class="contacts-list-contact">
                    <p>Real ones</p>
                    <p class="person">Me,</p>
                    <p class="person">Juliusz Słowacki,</p>
                    <p class="person">Cyprian Kamil Norwid</p>
                </div>

                <div class="contacts-list-contact">
                    <p>Słowacki private hati...</p>
                    <p class="person">Me,</p>
                    <p class="person">Cyprian Kamil Norwid</p>
                </div>
                <!-- <div class="contacts-list-contact">AAA</div>
                <div class="contacts-list-contact" id="contacts-list-contact-enabled">BBB</div>
                <div class="contacts-list-contact">CCC</div> -->
            </div>
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
                <button id="message-button">
                    <img src="static/img/send_bttn.png" alt="Send" srcset="">
                </button>
            </div>
        </div>
    </div>
</body>
</html>