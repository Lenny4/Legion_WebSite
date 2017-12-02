<?php /* Template Name: Buy Items Page */ ?>
<?php get_header(); ?>

<style>
    #shopDisplayItems li {
        border-right: 1px solid;
        border-left: 1px solid;
    }
</style>

<main class="col-md-8 col-md-offset-1" role="main">
    <!-- section -->
    <section class="background">
        <?php if (serverOnline() == false) { ?>
            <div class="alert alert-warning">
                <strong><?= get_field("server_offline_message", 253); ?></strong>
            </div>
        <?php } ?>
        <h1><?php the_title(); ?></h1>

        <?php if (have_posts()): while (have_posts()) : the_post(); ?>

            <!-- article -->
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="col-xs-12">
                    <?php
                    $_SESSION["shop"]->displayBuy();
                    ?>
                </div>

                <?php comments_template('', true); // Remove if you don't want comments ?>

                <br class="clear">

            </article>
            <!-- /article -->

        <?php endwhile; ?>

        <?php else: ?>

            <!-- article -->
            <article>

                <h2><?php _e('Sorry, nothing to display.', 'html5blank'); ?></h2>

            </article>
            <!-- /article -->

        <?php endif; ?>

    </section>
    <!-- /section -->
</main>

<?php get_sidebar(); ?>

<script>
    $(document).ready(function () {
        $("#main_character").change(function () {
            $("select.selectCharacter").val(this.value);
            $.post("/api/shop/shop.php",
                {
                    id: "changeSelectedCharacter",
                    character_name: this.value
                },
                function (data, status) {
                });
        });
    });
</script>

<?php get_footer(); ?>
