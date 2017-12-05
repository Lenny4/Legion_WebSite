<?php get_header(); ?>
<style>
    section.background {
        padding: 20px 0px;
    }
</style>
<main class="col-md-8 col-md-offset-1" role="main">
    <!-- section -->
    <section class="background">
        <?php if (serverOnline() == false AND (get_the_ID() == 16 OR get_the_ID() == 18)) { ?>
            <div class="alert alert-warning">
                <strong><?= get_field("information_update", 18); ?></strong>
            </div>
        <?php } ?>
        <h1><?php the_title(); ?></h1>
        <?php
        if (strpos($_SERVER[REQUEST_URI], 'password') !== false AND get_the_ID() == 18) {
            echo '<div class="alert alert-warning">
  <strong>Your password can have 16 characters maximum, otherwise you wouldn\'t be able to connect in the game</strong>
</div>';
        }
        ?>
        <?php if (have_posts()): while (have_posts()) : the_post(); ?>
            <!-- article -->
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <div class="col-xs-12">
                    <?php the_content(); ?>
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

<?php get_footer(); ?>
