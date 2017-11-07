<?php /* Template Name: Shop Admin */ ?>

<?php get_header(); ?>

<main class="col-md-8 col-md-offset-1" role="main">
    <!-- section -->
    <section class="background">

        <h1><?php the_title(); ?></h1>

        <?php if (have_posts()): while (have_posts()) : the_post(); ?>

            <!-- article -->
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <div class="col-xs-12">
                    <?php the_content(); ?>
                </div>

                <div class="col-md-6 col-xs-12">
                    <p class="h3">Add Item(s)</p>
                </div>

                <div class="col-md-6 col-xs-12">
                    <p class="h3">Add Item(s) Set</p>
                </div>

                <div class="col-md-6 col-xs-12">
                    <p class="h3">Add Custom Cat</p>
                </div>

                <div class="col-md-6 col-xs-12">
                    <p class="h3">Add Custom Item</p>
                </div>
                
                <div class="col-md-6 col-xs-12">
                    <p class="h3">Delete item</p>
                </div>

                <div class="col-md-6 col-xs-12">
                    <p class="h3">Delete item set</p>
                </div>

                <div class="col-md-6 col-xs-12">
                    <p class="h3">Delete cat</p>
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
