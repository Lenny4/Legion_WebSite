<!-- sidebar -->
<aside id="my_sidebar" style="overflow-y: auto"
       class="sidebar col-md-2 col-md-offset-0 col-xs-10 col-xs-offset-2 hidden-xs hidden-sm background"
       role="complementary">

    <?php
    echo '<div id="cart_shop"
    ';
    if ($_SESSION["shop"]->isEmpty()) {
        echo ' style="display:none;"';
    }
    echo '>';
    echo '<div class="col-xs-12 text-center" id="item_cart_collapse"><i onclick="animateRotate(this,360)" data-toggle="collapse" data-target="#item_cart" class="fa fa-chevron-circle-up fa-2x" aria-hidden="true"></i></div>
    <div class="col-xs-12 noPadding collapse" id="item_cart">';
    foreach ($_SESSION["shop"]->array as $item) {
        echo $item->displayCart();
    }
    ?>
    </div>
    <button type="button" class="btn btn-primary btn-block"><i class="fa fa-eye fa-2x" aria-hidden="true"></i>
    </button>
    <hr/>
    </div>
    <?php if (!is_user_logged_in()) { ?>
        <p class="h3">Login</p>
        <?= do_shortcode('[wpum_login_form psw_link="yes"]') ?>
        <hr/>
    <?php } ?>

    <p class="h3 text-center menu-sidebar">
        <i data-toggle="modal" data-target="#chooseLanguage" class="fa fa-language" aria-hidden="true"></i>
        <i class="fa fa-money" aria-hidden="true"></i>
        <i class="fa fa-area-chart" aria-hidden="true"></i>
    </p>
    <hr/>
    <?php if (is_user_logged_in()) { ?>
        <div style="display: contents" class="col-xs-12 profile_sidebar">
            <div class="row">
                <p class="h3" style="margin-top:0px;margin-left:20px;"><?= wp_get_current_user()->user_login; ?></p>
                <div style="display: inline-block; float: left">
                    <?php echo get_avatar(get_current_user_id(), 96); ?>
                </div>
                <p>
                    <?php echo wp_get_attachment_image(168, 'thumbnail', "", ["class" => "img-responsive img-float", "style" => "width:20px"]); ?>
                    <span style="margin-left: 10px"><?= formatNumber(get_user_meta(get_current_user_id(), 'buy_points')[0]); ?></span>
                </p>
                <p style="margin: 10px;">
                    <?php echo wp_get_attachment_image(169, 'thumbnail', "", ["class" => "img-responsive img-float", "style" => "width:20px"]); ?>
                    <span style="margin-left: 10px"><?= formatNumber(get_user_meta(get_current_user_id(), 'vote_points')[0]); ?></span>
                </p>
                <p>
                    <a href="<?= get_page_link(18); ?>">
                        <i style="color: #3B5998;" class="fa fa-address-card fa-2x" aria-hidden="true"></i>
                    </a>
                    <a href="<?php echo wp_logout_url(home_url()); ?>">
                        <i style="float: right;color: #b94a48;" class="fa fa-sign-out fa-2x" aria-hidden="true"></i>
                    </a>
                </p>
            </div>
        </div>
        <hr/>
    <?php } ?>
</aside>
<div class="col-sm-1-offset"></div>
<!-- /sidebar -->
