<?php /* Template Name: Buy Items Page */ ?>
<?php get_header(); ?>

<style>
    #shopDisplayItems li {
        border: 1px solid;
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
                    <div class="col-sm-6 col-sm-offset-3 col-xs-12">
                        <div id="ajaxLoaderShop" style="display: none">
                            <?php
                            $image = get_field("loading_img", $GLOBALS["shop_page_id"]);
                            echo wp_get_attachment_image($image["id"], 'medium', "", ["class" => "img-responsive", "style" => "max-width:100px;margin: 0 auto;"]);
                            ?>
                        </div>
                        <div id="display_buy_options"></div>
                    </div>
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
    function loadBuy() {
        $("#ajaxLoaderShop").show();
        $.post("/api/shop/shop.php",
            {
                id: "loadBuy"
            },
            function (data, status) {
                $("#ajaxLoaderShop").hide();
                $("#display_buy_options").html(data);
            });
    }

    function buyAllCart() {
        $("#ajaxLoaderShop").show();
        $.post("/api/shop/shop.php",
            {
                id: "buyAllCart"
            },
            function (data, status) {
                $("#ajaxLoaderShop").hide();
                $("#display_buy_options").html(data);
                //change display point
            });
    }

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
        $("select.selectCharacter").change(function () {
            var $item_id = $(this).attr("id").replace(/[^\d.]/g, '');
            $item_id = parseInt($item_id, 10);
            if ($(this).hasClass("item_set")) {
                $.post("/api/shop/shop.php",
                    {
                        id: "changeSelectedCharacter",
                        character_name: this.value,
                        type: "item_set",
                        item_id: $item_id
                    },
                    function (data, status) {
                    });
            } else if ($(this).hasClass("item")) {
                $.post("/api/shop/shop.php",
                    {
                        id: "changeSelectedCharacter",
                        character_name: this.value,
                        type: "item",
                        item_id: $item_id
                    },
                    function (data, status) {
                        loadBuy();
                    });
            }
        });
        $("select.quantity").change(function () {
            var $item_id = $(this).attr("id").replace(/[^\d.]/g, '');
            $item_id = parseInt($item_id, 10);
            var $type = "";
            var input = this;
            if ($(this).hasClass("item")) {
                $type = "item";
            } else if ($(this).hasClass("item_set")) {
                $type = "item_set";
            }
            $.post("/api/shop/shop.php",
                {
                    id: "changeQuantity",
                    quantity: this.value,
                    type: $type,
                    item_id: $item_id
                },
                function (data, status) {
                    $(input).val(data);
                    loadBuy();
                });
        });
        $("input.currency").change(function () {
            var $type = "";
            var $currency = "";
            if ($(this).hasClass("item_set")) {
                $type = "item_set";
            } else if ($(this).hasClass("item")) {
                $type = "item";
            }
            if ($(this).hasClass("vote")) {
                $currency = "vote";
            } else if ($(this).hasClass("buy")) {
                $currency = "buy";
            }
            $.post("/api/shop/shop.php",
                {
                    id: "changeCurrency",
                    currency: $currency,
                    type: $type,
                    item_id: $(this).attr("id")
                },
                function (data, status) {
                    loadBuy();
                });
        });
        loadBuy();
    });
</script>

<?php get_footer(); ?>
