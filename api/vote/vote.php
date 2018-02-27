<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/wp-config.php");

if (strpos($_SERVER["HTTP_REFERER"], $_SERVER["SERVER_NAME"]) == false) {//check if request is from my site
    echo("Not allowed");
    return;
}

if (!isset($_POST)) {
    echo("No data");
    return;
}

if (isset($_POST["id"]) AND $_POST["id"] == "vote") {
    $req = "SELECT * FROM `user_vote` WHERE `user_ip`='" . get_the_user_ip() . "' AND `website_id`=" . $_POST['websiteid'] . " AND `status`='voting'";
    $result = $GLOBALS["dbh"]->query($req);
    $count = $result->rowCount();
    if ($count == 0) {
        $req = "INSERT INTO `user_vote`(`user_id`, `user_ip`, `date`, `website_id`, `status`) VALUES (" . get_current_user_id() . ",'" . get_the_user_ip() . "',NOW()," . $_POST['websiteid'] . ", 'voting')";
        $GLOBALS["dbh"]->query($req);
        echo "vote";
    } else {
        echo "is_voting";
    }
}