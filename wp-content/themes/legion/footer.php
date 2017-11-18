</div>
<div class="hrMain">
    <hr class="hrMain dividerFooter"/>
</div>

<!-- footer -->
<footer class="footer row" role="contentinfo">
    <div class="col-sm-6 hidden-xs">
        <div class="col-xs-4">
            <?php
            $homePageId = get_option('page_on_front');
            $image = get_field("logo_footer", $homePageId);
            echo wp_get_attachment_image($image["id"], 'medium', "", ["class" => "img-responsive"]);
            ?>
        </div>
        <div class="col-xs-8">
            <div class="col-xs-12">
                <h1><?php bloginfo('name'); ?></h1>
            </div>
            <div class="col-xs-12">
                <ul class="social-network social-circle list-unstyled">
                    <li><a href="#" class="icoFacebook" title="Facebook"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="#" class="icoYoutube" title="Youtube"><i class="fa fa-youtube"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xs-12 footer_nav">
        <?php footer_nav(); ?>
    </div>
    <div class="col-xs-12 text-center">
        <!-- copyright -->
        <p class="copyright">
            &copy; <?php echo date('Y'); ?> Copyright <?php bloginfo('name'); ?>
        </p>
        <!-- /copyright -->
    </div>
    <a href="#body"><i class="fa fa-arrow-circle-up fa-3x buttonHeader" aria-hidden="true"></i></a>
</footer>
<!-- /footer -->

</div>
<!-- /wrapper -->

<?php wp_footer(); ?>

</body>
</html>
