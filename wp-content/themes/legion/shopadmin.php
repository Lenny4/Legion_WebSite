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

                <div class="col-md-6 col-xs-12 borderWhite">
                    <p class="h3">Add Item(s)</p>
                    <form id="previewItem">
                        <div class="form-group">
                            <input placeholder="Item id" type="number" class="form-control" name="item_id">
                        </div>
                        <div class="form-group">
                            <input placeholder="Price (Optionnal)" type="number" min="0" class="form-control"
                                   name="item_price">
                        </div>
                        <button type="submit" class="btn btn-default">Preview</button>
                    </form>
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

<div id="shopAdminModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<?php get_sidebar(); ?>

<script>

    function previewItem(data) {
        var modal = $('#shopAdminModal');
        $(modal).modal('show');
        var modalHeader = $(modal).find('.modal-title');
        var modalContent = $(modal).find('.modal-body');
        $(modalHeader).html("Add item");
        $(modalContent).html(data);
        $(modalContent).append("<button onclick='addItem(this)' id='addItem' class='btn btn-default'>Add to the shop</button>");
    }

    function addItem(button) {
        var form = 'id=' + $(button).attr("id") + "&" + $("#previewItem").serialize();
        $.post("/api/shop/admin.php", form, function (data, status) {
            if (status === "success") {
                var modal = $('#shopAdminModal');
                var modalContent = $(modal).find('.modal-body');
                $(modalContent).prepend('<div class="alert alert-warning alert-dismissable">\n' +
                    '  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n' +
                    '  <strong>' + data + '</strong>' +
                    '</div>');
            }
        });
    }

    $("form").submit(function (event) {
        event.preventDefault();
        var form = 'id=' + $(event.target).attr("id") + "&" + $(event.target).serialize();
        $.post("/api/shop/admin.php", form, function (data, status) {
            if (status === "success") {
                if ($(event.target).attr("id") === "previewItem") {
                    previewItem(data);
                }
            }
        });
    });

</script>

<?php get_footer(); ?>
