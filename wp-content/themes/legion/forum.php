<?php /* Template Name: Forum Page */ ?>
<?php get_header(); ?>

<style>
    #wp-link .query-notice {
        display: none;
    }

    .has-text-field #wp-link .query-results {
        top: 220px !important;
        background-color: #212528 !important;
    }

    #link-modal-title {
        background-color: #212528 !important;
    }

    #wp-link-close span.screen-reader-text {
        display: none;
    }

    #wp-link-wrap {
        background-color: #1d1d1d !important;
    }

    #wp-link .submitbox {
        background-color: #212528 !important;
    }

    #wp-link label input[type="text"], #wp-link .link-search-field {
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        background-color: #181b1e;
        -webkit-box-shadow: 0 1px rgba(255, 255, 255, .1), inset 0 1px 2px rgba(0, 0, 0, .6);
        -moz-box-shadow: 0 1px rgba(255, 255, 255, .1), inset 0 1px 2px rgba(0, 0, 0, .6);
        box-shadow: 0 1px rgba(255, 255, 255, .1), inset 0 1px 2px rgba(0, 0, 0, .6);
        border: 1px solid rgba(0, 0, 0, .9);
        padding: 5px;
        font-size: 12px;
    }

    #wp-link li:hover {
        background-color: #25c2f5 !important;
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

        <?php if (have_posts()): while (have_posts()) : the_post(); ?>
            <!-- article -->
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <p> Automatic translation :</p><?php echo do_shortcode('[gtranslate]'); ?>
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
<script>
    $(document).ready(function () {
        new PerfectScrollbar('#most-recent-results');
    });
</script>
<?php get_footer(); ?>
