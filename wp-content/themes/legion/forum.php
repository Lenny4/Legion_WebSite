<?php /* Template Name: Forum Page */ ?>
<?php get_header(); ?>
<style>
    section.background {
        padding: 20px 0px;
    }
    #bbpress-forums ul.bbp-lead-topic, #bbpress-forums ul.bbp-topics, #bbpress-forums ul.bbp-forums, #bbpress-forums ul.bbp-replies, #bbpress-forums ul.bbp-search-results {
        margin-top: 20px;
        border-radius: 6px;
        font-size: 15px;
    }

    #subscription-toggle {
        margin-left: 20px;
    }

    .forum div.bbp-template-notice {
        background-color: #fcf8e3 !important;
        border-color: #faebcc !important;
    }

    .forum div.bbp-template-notice p, div.bbp-template-notice p {
        color: #8a6d3b !important;
    }

    .bbp-pagination-count {
        color: white;
    }

    .forum div.bbp-template-notice.info {
        background-color: #d9edf7 !important;
        border-color: #bce8f1 !important;
    }

    .forum div.bbp-template-notice.info p {
        color: #31708f !important;
    }

    #bbpress-forums div.bbp-forum-header, #bbpress-forums div.bbp-topic-header, #bbpress-forums div.bbp-reply-header {
        background-color: rgba(0, 0, 0, 0.6);
    }

    #bbpress-forums div.odd, #bbpress-forums ul.odd {
        background-color: rgba(0, 0, 0, 0.6);
    }

    #bbpress-forums #bbp-your-profile fieldset label[for] {
        color: white;
    }

    #bbpress-forums #bbp-single-user-details #bbp-user-navigation li.current a {
        background-color: rgba(0, 0, 0, 0.6);
    }

    #bbpress-forums #bbp-your-profile fieldset select {
        color: white;
    }

    #bbpress-forums #bbp-your-profile fieldset select option {
        color: white;
    }

    .forum input {
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
        -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    }

    .forum form .button {
        background-color: #337ab7;
        border-color: #2e6da4;
        color: white;
        border-radius: 6px;
        height: initial;
        padding: 5px;
    }

    .forum a {
        color: white;
    }

    li.bbp-header, li.bbp-footer, li.bbp-body {
        background-color: rgba(0, 0, 0, 0.6) !important;
    }

    #bbpress-forums div.bbp-breadcrumb p a, #bbpress-forums div.bbp-topic-tags p a {
        color: white !important;
    }

    #bbpress-forums div.reply {
        background-color: rgba(0, 0, 0, 0.6) !important;
    }
    #bbpress-forums div.bbp-topic-author a.bbp-author-name, #bbpress-forums div.bbp-reply-author a.bbp-author-name{
        color:white;
    }

    ul.forum {
        background-color: rgba(0, 0, 0, 0.6) !important;
    }

    div.bbp-breadcrumb {
        clear: both;
    }

    #bbpress-forums fieldset.bbp-form legend {
        color: white !important;
    }

    .forum select {
        border-radius: 4px;
        background-color: rgba(31, 45, 55, 1);
    }

    .forum select option {
        border-radius: 4px;
        background-color: rgba(31, 45, 55, 1);
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
                    <?= do_shortcode("[bbp-forum-index]"); ?>
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
