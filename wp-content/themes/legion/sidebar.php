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
