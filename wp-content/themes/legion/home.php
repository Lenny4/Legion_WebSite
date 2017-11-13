<?php /* Template Name: Home Page */ ?>

<?php get_header(); ?>
<style>
    .hovereffect {
        width: 100%;
        height: 100%;
        float: left;
        overflow: hidden;
        position: relative;
        text-align: center;
        cursor: default;
    }

    .hovereffect .overlay {
        position: absolute;
        overflow: hidden;
        width: 80%;
        height: 80%;
        left: 10%;
        top: 10%;
        border-bottom: 1px solid #FFF;
        border-top: 1px solid #FFF;
        -webkit-transition: opacity 0.35s, -webkit-transform 0.35s;
        transition: opacity 0.35s, transform 0.35s;
        -webkit-transform: scale(0, 1);
        -ms-transform: scale(0, 1);
        transform: scale(0, 1);
    }

    .hovereffect:hover .overlay {
        opacity: 1;
        filter: alpha(opacity=100);
        -webkit-transform: scale(1);
        -ms-transform: scale(1);
        transform: scale(1);
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
    }

    .overlay img {
        display: none !important;
    }

    .hovereffect img {
        display: block;
        position: relative;
        -webkit-transition: all 0.35s;
        transition: all 0.35s;
    }

    .hovereffect:hover img {
        filter: url('data:image/svg+xml;charset=utf-8,<svg xmlns="http://www.w3.org/2000/svg"><filter id="filter"><feComponentTransfer color-interpolation-filters="sRGB"><feFuncR type="linear" slope="0.6" /><feFuncG type="linear" slope="0.6" /><feFuncB type="linear" slope="0.6" /></feComponentTransfer></filter></svg>#filter');
        filter: brightness(0.6);
        -webkit-filter: brightness(0.6);
    }

    .hovereffect h2 {
        text-transform: uppercase;
        text-align: center;
        position: relative;
        font-size: 17px;
        background-color: transparent;
        color: #FFF;
        padding: 1em 0;
        opacity: 0;
        filter: alpha(opacity=0);
        -webkit-transition: opacity 0.35s, -webkit-transform 0.35s;
        transition: opacity 0.35s, transform 0.35s;
        -webkit-transform: translate3d(0, -100%, 0);
        transform: translate3d(0, -100%, 0);
    }

    .hovereffect a, .hovereffect p {
        color: #FFF;
        padding: 1em 0;
        opacity: 0;
        filter: alpha(opacity=0);
        -webkit-transition: opacity 0.35s, -webkit-transform 0.35s;
        transition: opacity 0.35s, transform 0.35s;
        -webkit-transform: translate3d(0, 100%, 0);
        transform: translate3d(0, 100%, 0);
    }

    .hovereffect:hover a, .hovereffect:hover p, .hovereffect:hover h2 {
        opacity: 1;
        filter: alpha(opacity=100);
        -webkit-transform: translate3d(0, 0, 0);
        transform: translate3d(0, 0, 0);
    }
</style>
<main class="col-md-8 col-md-offset-1" role="main">
    <!-- section -->
    <section class="background" style="display: inline-block;">

        <?php if (have_posts()): while (have_posts()) : the_post(); ?>

            <!-- article -->
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <div class="col-md-7 col-sm-6 col-xs-12">
                    <div class="col-xs-12">
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
                    </div>
                    <div class="col-md-5 col-sm-6 col-xs-12">
                        <?php
                        if (is_user_logged_in()) { ?>
                            <style>
                                .fb_iframe_widget, .fb_iframe_widget span, .fb_iframe_widget iframe, div._2p3a {
                                    max-width: 100% !important;
                                }
                            </style>
                            <p class="h3">Facebook</p>
                            <?php
                            $homePageId = get_option('page_on_front');
                            $fbUrl = get_field("url_facebook_page", $homePageId);
                            echo do_shortcode('[sfp-page-plugin url=' . $fbUrl . ']') ?>
                        <?php } else { ?>
                            <p class="h3">Register</p>
                            <?= do_shortcode('[wpum_register form_id="" login_link="yes" psw_link="yes" register_link="no" ]') ?>
                        <?php }
                        ?>
                    </div>

                    <?php the_content(); ?>

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
    <a href="<?= get_page_link(93) ?>">
        <div class="col-md-6 col-xs-12" style="margin-top: 20px">
            <div class="col-xs-12 background">
                <p class="h1">Shop</p>
                <div class="col-xs-12">
                    <?php
                    $homePageId = get_option('page_on_front');
                    $image = get_field("shop", $homePageId);
                    echo wp_get_attachment_image($image["id"], 'full', "", ["class" => "img-responsive img-rounded center-block", "style" => "padding-bottom:10px;"]);
                    ?>
                </div>
            </div>
        </div>
    </a>
    <div class="col-md-6 col-xs-12" style="margin-top: 20px">
        <div class="col-xs-12 background">
            <p class="h1">Royaume</p>
            <hr/>
            <p class="h2">Nom du Royaume :
                <?php
                if (serverOnline() == true) { ?>
                    <span class="h3" style="color: green">Online</span>
                <?php } else { ?>
                    <span class="h3" style="color: red">Offline</span>
                <?php } ?>
            </p>
            <p style="font-size: 20px">Rate XP | Loot | Gold : x10</p>
            <p style="font-size: 20px">Free first lvl 110</p>
        </div>
        <div class="col-xs-12 background" style="margin-top: 20px">
            <p class="h1">Classement</p>
            <table>
                <tr style="border-bottom: solid white 1px;">
                    <td>Name</td>
                    <td>Wins</td>
                    <td>Losses</td>
                    <td>Ranking</td>
                </tr>
                <?php
                $tabLadder = getLadder();
                foreach ($tabLadder as $row) {
                    echo "<tr>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td>" . $row["win"] . "</td>";
                    echo "<td>" . $row["losses"] . "</td>";
                    echo "<td>" . $row["ranking"] . "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
</main>

<?php get_sidebar(); ?>

<?php get_footer(); ?>

