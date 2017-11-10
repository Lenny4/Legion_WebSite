<?php /* Template Name: Shop Page */ ?>
<?php get_header(); ?>

<main class="col-md-8 col-md-offset-1" role="main">
    <!-- section -->
    <section class="background shop">

        <h1 class="text-center"><?php the_title(); ?></h1>

        <?php if (have_posts()): while (have_posts()) : the_post(); ?>

            <!-- article -->
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <div class="col-xs-12">
                    <?php the_content(); ?>
                    <?php $allItemClasses = getAllItemClasses(); ?>
                    <div class="row">
                        <div class="col-sm-3 col-xs-12">
                            <div class="panel-group" id="accordion">
                                <?php foreach ($allItemClasses as $key => $itemClasse) { ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion"
                                                   href="#<?= $itemClasse["name"]; ?>"><?= $itemClasse["name"]; ?></a>
                                            </h4>
                                        </div>
                                        <div id="<?= $itemClasse["name"]; ?>" class="panel-collapse collapse">
                                            <div class="panel-body">
                                                <table class="table">
                                                    <?php foreach ($itemClasse["subclasses"] as $subItemClasse) { ?>
                                                        <tr>
                                                            <td class="first">
                                                                <a class="subItemClasse clickable"><?= $subItemClasse->name; ?></a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                            <div class="col-sm-9 col-xs-12">

                            </div>
                        </div>
                    </div>

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

