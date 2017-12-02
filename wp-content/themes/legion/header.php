<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title><?php wp_title(''); ?><?php if (wp_title('', false)) {
            echo ' :';
        } ?><?php bloginfo('name'); ?></title>

    <link href="//www.google-analytics.com" rel="dns-prefetch">
    <link href="<?php echo get_template_directory_uri(); ?>/img/icons/favicon.ico" rel="shortcut icon">
    <link href="<?php echo get_template_directory_uri(); ?>/img/icons/touch.png" rel="apple-touch-icon-precomposed">

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php bloginfo('description'); ?>">

    <link rel="stylesheet"
          href="<?php echo get_template_directory_uri(); ?>/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet"
          href="<?php echo get_template_directory_uri(); ?>/library/scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet"
          href="<?php echo get_template_directory_uri(); ?>/fonts/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <script src="<?php echo get_template_directory_uri(); ?>/fonts/jquery-3.2.1/jquery.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/fonts/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/library/scrollbar/perfect-scrollbar.min.js"></script>

    <?php wp_head(); ?>
</head>
<body id="body" <?php body_class(); ?>>
<?php //wp_set_password('password', 1); ?>
<i id="showHideSideBar" onclick="hideShowSideBar(false)"
   class="fa fa-arrow-circle-left fa-2x buttonSideBar hidden-lg hidden-md" aria-hidden="true"></i>
<!-- wrapper -->
<div class="wrapper">

    <!-- header -->
    <header class="header clear" role="banner">
        <nav class="navbar navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="<?= get_home_url(); ?>">
                        <?php
                        $homePageId = get_option('page_on_front');
                        $image = get_field("logo", $homePageId);
                        echo wp_get_attachment_image($image["id"], 'medium', "", ["class" => "mainLogoHomePage hidden-xs"]);
                        ?>
                    </a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <?php header_left_nav(); ?>
                    <?php header_right_nav(); ?>
                </div>
            </div>
        </nav>
        <!-- /nav -->
        <?php
        $homePageId = get_option('page_on_front');
        $image = get_field("parallax", $homePageId);
        $video = get_field("video_parallax", $homePageId);
        ?>
        <?php if ($video AND !wp_is_mobile()) {
            echo '
            <div class="parallax">
            <video class="videoMainParallax" autoplay loop="loop" src="' . $video["url"] . ' "></video>
            </div>';
        } else { ?>
            <div class="parallax" style="background-image: url('<?= $image["url"] ?>');"></div>
        <?php } ?>
    </header>
    <!-- /header -->
    <div class="hrMain">
        <hr class="hrMain dividerHeader"/>
    </div>
    <?php
    $homePageId = get_option('page_on_front');
    $image = get_field("main_paralaxx", $homePageId);
    ?>
    <div class="mainContent row mainParallax" style="background-image: url('<?= $image["url"] ?>');">
        <?php
        if (isWowAdmin()) {
            $req = $GLOBALS["dbh"]->query('SELECT * FROM `ask_new_items` WHERE `answer` IS NULL');
            if ($req->rowCount() > 0) {
                echo '<div class="col-sm-6 col-sm-offset-3 col-xs-12" style="margin-top: 20px; z-index:500">';
                while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
                    if ($data["item_id"] != null) {
                        echo '
                    <div class="alert alert-success">
                        <strong>Asked Item : </strong> ' . $data["item_id"] . ', number : ' . $data["number"] . ' 
                        <a target="_blank" href="http://www.wowhead.com/item=' . $data["item_id"] . '">Link</a>
                        <a onclick="askedItemAdded(' . $data["item_id"] . ',this)" class="pointer">Item added</a>
                        <a onclick="askedItemRefused(' . $data["item_id"] . ',this)" class="pointer">Item refused</a>
                    </div>
                    ';
                    } else {
                        echo '
                    <div class="alert alert-success">
                        <strong>Asked Item set : </strong> ' . $data["item_set_id"] . ', number : ' . $data["number"] . ' 
                        <a target="_blank" href="http://www.wowhead.com/item-set=' . $data["item_set_id"] . '">Link</a>
                        <a onclick="askedItemSetAdded(' . $data["item_set_id"] . ',this)" class="pointer">Item set added</a>
                        <a onclick="askedItemSetRefused(' . $data["item_set_id"] . ',this)" class="pointer">Item set refused</a>
                    </div>
                    ';
                    }
                }
                echo '</div>';
            }
        }
        if (is_user_logged_in()) {
            $req = $GLOBALS["dbh"]->query('SELECT * FROM `message_header` WHERE `user_id`=' . get_current_user_id());
            if ($req->rowCount() > 0) {
                echo '<div class="col-sm-6 col-sm-offset-3 col-xs-12" style="margin-top: 20px; z-index:500">';
                while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
                    if ($data["item_id"] != null) {
                        if ($data["message"] == "item_added") {
                            $message = get_field("item_added", $GLOBALS["shop_page_id"]);
                            echo '
                            <div class="alert alert-success alert-dismissable">
                              <a id="' . $data["id"] . '" href="#" class="close deleteMessageHeader" data-dismiss="alert" aria-label="close">&times;</a>
                              <strong>' . $message . ' ' . $data["item_id"] . '</strong>
                            </div>
                            ';
                        } elseif ($data["message"] == "item_refused") {
                            $message = get_field("item_refused", $GLOBALS["shop_page_id"]);
                            echo '
                            <div class="alert alert-danger alert-dismissable">
                              <a id="' . $data["id"] . '" href="#" class="close deleteMessageHeader" data-dismiss="alert" aria-label="close">&times;</a>
                              <strong>' . $message . ' ' . $data["item_id"] . '</strong>
                            </div>
                            ';
                        }
                    }
                    if ($data["item_set_id"] != null) {
                        if ($data["message"] == "item_set_added") {
                            $message = get_field("item_set_added", $GLOBALS["shop_page_id"]);
                            echo '
                            <div class="alert alert-success alert-dismissable">
                              <a id="' . $data["id"] . '" href="#" class="close deleteMessageHeader" data-dismiss="alert" aria-label="close">&times;</a>
                              <strong>' . $message . ' ' . $data["item_set_id"] . '</strong>
                            </div>
                            ';
                        } elseif ($data["message"] == "item_set_refused") {
                            $message = get_field("item_set_added", $GLOBALS["shop_page_id"]);
                            echo '
                            <div class="alert alert-danger alert-dismissable">
                              <a id="' . $data["id"] . '" href="#" class="close deleteMessageHeader" data-dismiss="alert" aria-label="close">&times;</a>
                              <strong>' . $message . ' ' . $data["item_set_id"] . '</strong>
                            </div>
                            ';
                        }
                    }
                }
                echo '</div>';
            }
        }
        ?>
