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
    <link rel="stylesheet" href="static/css/dashboard_page_media_query.css">
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
            <img src="static/img/svg/power-off-solid.svg" alt="Log off" srcset="">
        </button>
        <button id="channel-display">
            <img src="static/img/svg/comments-solid.svg" alt="Log off" srcset="">
        </button>
        <?php
            if ($_SESSION['user-is_admin']) {
                echo "<button id=\"user-display\">
                          <img src=\"static/img/svg/users-solid.svg\" alt=\"Log off\" srcset=\"\">
                      </button>";
            }
        ?>
    </div>
    <div id="main">
        <div id="main-left">
            <?php
                if ($_SESSION['user-is_admin']) {
                    echo "<div id=\"users-list\">
                              <div id=\"users-list-admins\">
                                  <input id=\"users-list-show-admin\" type=\"checkbox\">
                                  <label for =\"users-list-show-admin\">
                                      <img src=\"static/img/svg/angle-right-solid.svg\" alt=\"tick\" srcset=\"\">
                                      <p>Admins</p>
                                  </label>
                                  <div id=\"users-list-admins-list\"></div>
                              </div>
                              <div id=\"users-list-users\">
                                  <input id=\"users-list-show-user\" type=\"checkbox\">
                                      <label for =\"users-list-show-user\">
                                      <img src=\"static/img/svg/angle-right-solid.svg\" alt=\"tick\" srcset=\"\">
                                      <p>Users</p>
                                  </label>
                                  <div id=\"users-list-users-list\"></div>
                              </div>
                          </div>";
                }
            ?>
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
                                      <input id=\"contacts-add-contact-input\" placeholder=\"Add chat...\">
                                      <div id=\"contacts-add-contact-send\">
                                          <img src=\"static/img/svg/square-plus-solid.svg\" alt=\"tick\" srcset=\"\">
                                      </div>
                                  </div>
                              </div>";
                    }
                ?>
            </div>
            <div id="user-info">
                <div id="user-info-container">
                    <div>
                        <div></div>
                        <p><?php echo $_SESSION['user-name']?></p>
                    </div>
                    <div>
                        <div></div>
                        <p><?php echo $_SESSION['user-email']?></p>
                    </div>
                </div>
            </div>
        </div>
        <div id="main-right">
            <div id="message-place"></div>
            <div id="message-form">
                <p id="message-letter-count"></p>
                <div id="message-input-container">
                    <textarea name="input-message" id="message-input" placeholder="Share your thoughts..."></textarea>
                </div>
                <button id="message-button">
                    <img src="static/img/svg/paper-plane-solid.svg" alt="Send" srcset="">
                </button>
            </div>
        </div>
    </div>
</body>
</html>