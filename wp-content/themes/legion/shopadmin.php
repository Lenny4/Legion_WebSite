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
                    <p class="h3">Add Item</p>
                    <form id="previewItem">
                        <div class="form-group">
                            <input placeholder="Item id" type="number" class="form-control" name="item_id">
                        </div>
                        <div class="form-group">
                            <input placeholder="Price (Optionnal)" type="number" min="0" class="form-control"
                                   name="item_price">
                        </div>
                        <div class="form-group">
                            <label for="sel1">Can buy with vote point</label>
                            <select class="form-control" name="vote">
                                <option selected value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-default">Preview</button>
                    </form>
                </div>

                <div class="col-md-6 col-xs-12 borderWhite">
                    <p class="h3">Add Item Set</p>
                    <form id="previewItemSet">
                        <div class="form-group">
                            <input placeholder="Item Set id" type="number" class="form-control" name="item_set_id">
                        </div>
                        <div class="form-group">
                            <input placeholder="Price (Optionnal)" type="number" min="0" class="form-control"
                                   name="item_set_price">
                        </div>
                        <div class="form-group">
                            <label for="sel1">Can buy with vote point</label>
                            <select class="form-control" name="vote">
                                <option selected value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-default">Preview</button>
                    </form>
                </div>

                <div class=" col-md-6 col-xs-12 borderWhite">
                    <?php
                    if (isset($_FILES) AND isset($_POST["addHomeItem"])) {
                        $url = uploadFile($_FILES);
                        if ($url != false) {
                            $homeItem = new item_home();
                            $homeItem->name = $_POST["item_name"];
                            $homeItem->price = intval($_POST["item_price"]);
                            $homeItem->phpclasse = $_POST["item_phpClasse"];
                            $homeItem->vote = $_POST["vote"];
                            $homeItem->image = $url;
                            $req = $homeItem->generateInsertRequest();
                            $GLOBALS["dbh"]->query($req); ?>
                            <div class="alert alert-success alert-dismissable">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                Item added !
                            </div>
                            <?php
                        } else {
                            echo "Error upload image";
                        }
                    }
                    ?>
                    <p class="h3">Home page item</p>
                    <form method="post" id="addHomeItem" enctype="multipart/form-data">
                        <div class="form-group">
                            <input type="file" name="file">
                        </div>
                        <div class="form-group">
                            <input placeholder="Item name (en)" type="text" class="form-control" name="item_name">
                        </div>
                        <div class="form-group">
                            <input placeholder="Item price" type="number" class="form-control" name="item_price">
                        </div>
                        <div class="form-group">
                            <input placeholder="Item php classe without .php" type="text" class="form-control"
                                   name="item_phpClasse">
                            https://stackoverflow.com/questions/4578335/creating-php-class-instance-with-a-string/4578343
                        </div>
                        <div class="form-group">
                            <label for="sel1">Can buy with vote point</label>
                            <select class="form-control" name="vote">
                                <option selected value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <button name="addHomeItem" type="submit" class="btn btn-default">Add item</button>
                    </form>
                </div>
                <div class=" col-md-6 col-xs-12 borderWhite">
                    <p class="h3">Define normal price for object (need to be store in bdd)</p>
                    Create a button change all current item price
                </div>
                <div class=" col-md-6 col-xs-12 borderWhite">
                    <p class="h3">Promotion (will apply to all according to the category choose item itemset home
                        item)</p>
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
    $("form").submit(function (event) {
        if ($(event.target).attr("id") === "addHomeItem") {
            return;
        }
        event.preventDefault();
        var form = 'id=' + $(event.target).attr("id") + "&" + $(event.target).serialize();
        $("*").addClass("progressWait");
        $.post("/api/shop/shop.php", form, function (data, status) {
            $("*").removeClass("progressWait");
            if (status === "success") {
                console.log(data);
                if ($(event.target).attr("id") === "previewItem") {
                    previewItem(data);
                }
                if ($(event.target).attr("id") === "previewItemSet") {
                    previewItemSet(data);
                }
                if ($(event.target).attr("id") === "addHomeItem") {
                    previewItemSet(data);
                }
            }
        });
    });
</script>

<?php get_footer(); ?>
