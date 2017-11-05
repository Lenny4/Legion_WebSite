<?php /* Template Name: Home Page */ ?>

<?php get_header(); ?>

<main class="col-md-8 col-md-offset-1" role="main">
    <!-- section -->
    <section style="display: inline-block;">

        <h1><?php the_title(); ?></h1>

        <?php if (have_posts()): while (have_posts()) : the_post(); ?>

            <!-- article -->
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <div class="col-xs-7">
                    <p class="h3">Last Post</p>

                    <div id="mainCarousel" class="carousel slide" data-ride="carousel">
                        <?php
                        $lasteArticles = wp_get_recent_posts(5);
                        $nbArticles = sizeof($lasteArticles);
                        ?>
                        <!-- Indicators -->
                        <ol class="carousel-indicators">
                            <?php $i = 0;
                            foreach ($lasteArticles as $article) { ?>
                                <?php if ($i == 0) { ?>
                                    <li data-target="#mainCarousel" data-slide-to="<?= $i; ?>" class="active"></li>
                                <?php } else { ?>
                                    <li data-target="#mainCarousel" data-slide-to="<?= $i; ?>"></li>
                                <?php } ?>
                                <?php $i++;
                            }
                            ?>
                        </ol>

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner">
                            <?php
                            $i = 0;
                            foreach ($lasteArticles

                            as $article) {
                            $id = $article["ID"];
                            $image = get_the_post_thumbnail_url($id);
                            $title = $article["post_title"];
                            $content = $article["post_content"];
                            $link = $article["guid"];
                            if ($i == 0){ ?>
                            <div class="item active">
                                <?php }else{ ?>
                                <div class="item">
                                    <?php } ?>
                                    <p class="h3"><?= $title; ?></p>
                                    <div class="hovereffect">
                                        <img class="img-responsive" src="<?= $image; ?>" alt="<?= $title; ?>">
                                        <div class="overlay">
                                            <p class="h4">
                                                <a href="<?= $link; ?>">Read More</a>
                                            </p>
                                            <?= $content; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php $i++;
                                }
                                ?>
                            </div>

                            <!-- Left and right controls -->
                            <a class="left carousel-control" href="#mainCarousel" data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="right carousel-control" href="#mainCarousel" data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-5">
                        <p class="h3">Register</p>
                        <?= do_shortcode('[wpum_register form_id="" login_link="yes" psw_link="yes" register_link="no" ]') ?>
                    </div>

                    <?php the_content(); ?>

                    <?php comments_template('', true); // Remove if you don't want comments ?>

                    <br class="clear">

                    <?php edit_post_link(); ?>

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

