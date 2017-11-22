<!-- sidebar -->
<aside id="my_sidebar" style="overflow-y: auto"
       class="sidebar col-md-2 col-md-offset-0 col-xs-10 col-xs-offset-2 hidden-xs hidden-sm background"
       role="complementary">

    <?php if (!is_user_logged_in()) { ?>
        <p class="h3">Login</p>
        <?= do_shortcode('[wpum_login_form]') ?>
        <hr/>
    <?php } ?>

    <p data-toggle="modal" data-target="#chooseLanguage" class="h3 clickable text-center">Change Language</p>
    <hr/>
    <a href="<?php echo wp_logout_url( home_url() ); ?>">Logout</a>
</aside>
<div class="col-sm-1-offset"></div>
<!-- /sidebar -->
