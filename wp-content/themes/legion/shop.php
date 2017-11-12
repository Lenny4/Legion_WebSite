<?php /* Template Name: Shop Page */ ?>
<?php get_header(); ?>

<main class="col-md-8 col-md-offset-1" role="main">
    <!-- section -->
    <section class="background shop">

        <h1 class="text-center"><span class="clickable overGreen" id="mainShopTitle"><?php the_title(); ?></span></h1>

        <?php if (have_posts()): while (have_posts()) : the_post(); ?>

            <!-- article -->
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <p>Automatic translation :</p><?php echo do_shortcode('[gtranslate]'); ?>
                <div class="col-xs-12">
                    <?php the_content(); ?>
                    <?php $allItemClasses = getAllItemClasses(); ?>
                    <div class="row">
                        <div class="col-sm-3 col-xs-12">
                            <div class="hidden-lg hidden-md hidden-sm">
                                <p class="h3 text-center">
                                    <span id="hideShowCategoryShop" onclick="toggleCategoryShop(this)"
                                          class="clickable overGreen">
                                        Hide Category
                                    </span>
                                    <span class="hidden">
                                        Show Category
                                    </span>
                                </p>
                            </div>
                            <div class="panel-group" id="accordion">
                                <?php foreach ($allItemClasses as $key => $itemClasse) { ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion"
                                                   href="#<?= $itemClasse["class_id"]; ?>"><?= $itemClasse["name"]; ?></a>
                                            </h4>
                                        </div>
                                        <div id="<?= $itemClasse["class_id"]; ?>" class="panel-collapse collapse">
                                            <div class="panel-body">
                                                <table class="table">
                                                    <?php foreach ($itemClasse["subclasses"] as $subItemClasse) { ?>
                                                        <tr>
                                                            <td class="first">
                                                                <a id="subItemClasse"
                                                                   data-sub-class-id="<?= $subItemClasse->subclass; ?>"
                                                                   class="subItemClasse clickable resetLanguage"><?= $subItemClasse->name; ?></a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <hr style="margin-top: 0px" class="hidden-lg hidden-md hidden-sm"/>
                        </div>
                        <div class="col-sm-9 col-xs-12">
                            <div id="ajaxLoaderShop" style="display: none">
                                <?php
                                $image = get_field("loading_img", get_the_ID());
                                echo wp_get_attachment_image($image["id"], 'medium', "", ["class" => "img-responsive", "style" => "max-width:100px;margin: 0 auto;"]);
                                ?>
                            </div>
                            <div style="display: none" id="helpFilterNameShop">
                                <p onclick="changeArrowShop(this)" class="clickable text-center h4"
                                   style="font-family: inherit; margin-top: 0px" data-toggle="collapse"
                                   data-target="#helpFiterShop">How
                                    to serach ? <i class="fa fa-arrow-down" aria-hidden="true"></i></p>
                                <div id="helpFiterShop" class="collapse">
                                    <p>Explain how to search</p>
                                </div>
                            </div>
                            <input style="display: none" class="form-control" id="filterNameShop" type="text">
                            <div class="col-xs-12" id="shopDisplayError"></div>
                        </div>
                        <ul class="list-group" id="filterNameListShop">
                            <div id="shopDisplayItems"></div>
                        </ul>
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

<div id="shopModal" class="modal fade" role="dialog">
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
    function showFilterShop() {
        $("#filterNameShop").show();
        $("#helpFilterNameShop").show();
    }

    function showAlertMessage() {
        $("#shopDisplayError").show();
    }

    function hideAllHeaderShop() {
        $("#filterNameShop").hide();
        $("#helpFilterNameShop").hide();
        $("#shopDisplayError").hide();
    }

    function changeArrowShop($this) {
        var $arrow = $($this).children();
        if ($($arrow).hasClass("fa-arrow-down")) {
            $($arrow).removeClass("fa-arrow-down");
            $($arrow).addClass("fa-arrow-up");
        } else {
            $($arrow).removeClass("fa-arrow-up");
            $($arrow).addClass("fa-arrow-down");
        }
    }

    function toggleCategoryShop($this) {
        var $menu = $($this).parent().parent().next();
        if ($($menu).hasClass("hidden-xs")) {
            $($menu).removeClass("hidden-xs");
        } else {
            $($menu).addClass("hidden-xs");
        }
        var $previousValue = $($this).text();
        $($this).text($($this).next().text());
        $($this).next().text($previousValue)
    }

    function showMoreShop($this) {
        $("*").addClass("progressWait");
        console.log($($this).attr('data-show'));
        var id = JSON.parse($($this).attr('data-show')).id;
        var value = JSON.parse($($this).attr('data-show')).value;
        $.post("/api/shop/shop.php",
            {
                id: id,
                item_id: value,
                item_set_id: value
            },
            function (data, status) {
                $("*").removeClass("progressWait");
                if (status === "success") {
                    var modal = $('#shopModal');
                    $(modal).modal('show');
                    var modalHeader = $(modal).find('.modal-title');
                    var modalContent = $(modal).find('.modal-body');
                    $(modalHeader).html("");
                    $(modalContent).html(data);
                }
            });
    }

    function hideCategoryIfOnPhone() {
        if ($(window).width() <= 768) {
            $("#hideShowCategoryShop").click();
        }
    }

    function hideAjaxLoaderShop() {
        $("#ajaxLoaderShop").hide();
    }

    function showAjaxLoaderShop() {
        $("#ajaxLoaderShop").show();
    }

    $("a.subItemClasse").click(function (e) {
        hideCategoryIfOnPhone();
        showAjaxLoaderShop();
        $("*").addClass("progressWait");
        var target = e.target;
        if (!$(target).hasClass("clickable")) {
            target = $(target).parent().parent();
        }
        var $classId = $(target).parent().parent().parent().parent().parent().parent().attr("id");
        var $id = $(target).attr('id');
        var $subClassId = $(target).attr('data-sub-class-id');
        $.post("/api/shop/shop.php",
            {
                id: $id,
                subClassId: $subClassId,
                classId: $classId
            },
            function (data, status) {
                $("*").removeClass("progressWait");
                hideAllHeaderShop();
                hideAjaxLoaderShop();
                if (data !== 'Error !' && data !== 'No Result !') {
                    showFilterShop();
                    $("#shopDisplayItems").html(data);
                } else {
                    showAlertMessage();
                    $("#shopDisplayItems").html("");
                    $("#shopDisplayError").html('<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>' + data + '</strong></div>');
                }
            });
    });

    $("#mainShopTitle").click(function (e) {
        $("#shopDisplayItems").html("");
        hideAllHeaderShop();
    });

    $(document).ready(function () {
        $("#filterNameShop").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#filterNameListShop li").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>

<?php get_footer(); ?>

