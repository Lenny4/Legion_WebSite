<!-- sidebar -->
<aside class="sidebar col-md-2 col-md-offset-0 col-xs-10 col-xs-offset-2 hidden-xs hidden-sm background"
       role="complementary">

    <?php if (!is_user_logged_in()) { ?>
        <p class="h3">Login</p>
        <?= do_shortcode('[wpum_login_form]') ?>
        <hr/>
    <?php } ?>

    <p data-toggle="modal" data-target="#chooseLanguage" class="h3 clickable text-center">Change Language</p>
    <hr/>
    <?php
    $homePageId = get_option('page_on_front');
    $image = get_field("horde/alliance", $homePageId);
    $link = wp_get_attachment_image_src($image["id"],"full")[0];
    ?>
    <div style="background-image: url('<?= $link; ?>');background-size: 100% 100%;background-repeat: no-repeat;">
        <p class="h3 text-center">Online Player</p>
        <?php $tabOnline = getOnlinePlayer(); ?>
        <div id="onlinePlayer" class="row">
            <div style="width: <?= $tabOnline["pBlue"] ?>%"><p class="text-center h4"><?= $tabOnline["blue"] ?></p>
            </div>
            <div style="width: <?= $tabOnline["pRed"] ?>%"><p class="text-center h4"><?= $tabOnline["red"] ?></p></div>
            <div style="height: 10px; width: 100%"></div>
            <div style="border-radius: 5px 0px 0px 5px;background-color:darkblue; width: <?= $tabOnline["pBlue"] ?>%">
                <span><?= $tabOnline["pBlue"] ?>%</span></div>
            <div style="border-radius: 0px 5px 5px 0px;background-color:darkred; width: <?= $tabOnline["pRed"] ?>%">
                <span><?= $tabOnline["pRed"] ?>%</span></div>
        </div>
        <br/>
    </div>
    <hr/>

    <!-- Modal -->
    <div id="chooseLanguage" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="qtransSelector"
                         class=""><?php qtranxf_generateLanguageSelectCode('image'); //‘image’, ‘text’, ‘both’, and ‘dropdown’  ?></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

</aside>

<div class="col-sm-1-offset"></div>
<!-- /sidebar -->
