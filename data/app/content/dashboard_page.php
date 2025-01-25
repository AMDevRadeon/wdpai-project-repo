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
    <?php
        if ($_SESSION['user-is_admin']) {
            echo "<link rel=\"stylesheet\" href=\"static/css/dashboard_page_admin.css\">";
        }
    ?>
    <script>
        let self_name = "<?php echo $_SESSION['user-name'];?>";
        let self_email = "<?php echo $_SESSION['user-email'];?>";
    </script>
    <?php
        if ($_SESSION['user-is_admin']) {
            echo "<script defer type=\"text/javascript\" src=\"static/js/dashboard_admin.js\"></script>";
        }
        else {
            echo "<script defer type=\"text/javascript\" src=\"static/js/dashboard.js\"></script>";
        }
    ?>
    <title>WeChat - <?php echo $_SESSION['user-name']?></title>
</head>
<body>
    <div id="menubar-left">
        <button id="logout">
            <img src="static/img/off_bttn.png" alt="Log off" srcset="">
        </button>
    </div>
    <div id="main">
        <div id="main-left">
            <div id="contacts-list">
                <div id="contact_global" class="contacts-list-contact contacts-list-contact-enabled">
                    <p>Global chat</p>
                    <div class="persons">
                        <p class="person">Everyone</p>
                    </div>
                </div>
                <?php
                    if ($_SESSION['user-is_admin']) {
                        echo "<div id=\"contacts-add-contact\" class=\"contacts-list-contact\">
                                  <div id=\"contacts-add-contact-line\">
                                      <input id=\"contacts-add-contact-input\">
                                      <div id=\"contacts-add-contact-send\"></div>
                                  </div>
                              </div>";
                    }
                ?>
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