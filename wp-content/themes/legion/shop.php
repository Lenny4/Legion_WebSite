<?php /* Template Name: Shop Page */ ?>
<?php get_header(); ?>

<main class="col-md-8 col-md-offset-1" role="main">
    <!-- section -->
    <section class="background shop">

        <h1 class="text-center">
            <span class="clickable overGreen" id="mainShopTitle">
                <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                <?php the_title(); ?>
            </span>
        </h1>

        <?php if (have_posts()): while (have_posts()) : the_post(); ?>

            <!-- article -->
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <p>Automatic translation :</p><?php echo do_shortcode('[gtranslate]'); ?>
                <div class="col-xs-12">
                    <?php the_content(); ?>
                    <?php $allItemClasses = getAllItemClasses(); ?>
                    <?php $allItemSetClasses = getAllItemSetClasses(); ?>
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
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#setofitemcollapse"
                                               class="collapsed" aria-expanded="false">Set of items</a>
                                        </h4>
                                    </div>
                                    <div id="setofitemcollapse" class="panel-collapse collapse" aria-expanded="false"
                                         style="height: 0px;">
                                        <div class="panel-body">
                                            <table class="table">
                                                <?php foreach ($allItemSetClasses as $key => $itemSetClasse) { ?>
                                                    <tr class="">
                                                        <td class="first">
                                                            <a id="subItemSetClasse"
                                                               data-sub-class-id="<?= $itemSetClasse; ?>"
                                                               class="subItemSetClasse clickable resetLanguage"><?= $itemSetClasse; ?></a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <hr class="hrShopCategory"/>
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
                                <p onclick="changeArrowShop(this)" class="clickable text-center h4 overGreen"
                                   style="font-family: inherit; margin-top: 0px" data-toggle="collapse"
                                   data-target="#helpFiterShop">How to search ?
                                    <i class="fa fa-arrow-down" aria-hidden="true"></i></p>
                                <div id="helpFiterShop" class="collapse">
                                    <p>Explain how to search</p>
                                </div>
                            </div>
                            <input placeholder="Search" style="display: none" class="form-control" id="filterNameShop"
                                   type="text">
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

    function showAlertMessage(data) {
        $("#shopDisplayError").show();
        $("#shopDisplayItems").html("");
        $("#shopDisplayError").html('<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>' + data + '</strong></div>');
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
        var id = JSON.parse($($this).attr('data-show')).id;
        var value = JSON.parse($($this).attr('data-show')).value;
        console.log(id);
        console.log(value);
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

    function loadHomePageShop() {
        $("#shopDisplayItems").html("");
        hideAllHeaderShop();
        showAjaxLoaderShop();

        hideAjaxLoaderShop();
    }

    function sameHeight($height=0) {
        if ($(window).width() <= 768) {
            return;
        }
        var $maxHeight = 0;
        var $allContent = $("#shopDisplayItems").first();
        if ($height === 0) {
            $($allContent).children('a').each(function () {
                $li = $(this).children();
                if ($($li).height() > $maxHeight) {
                    $maxHeight = $($li).height();
                }
            });
            sameHeight($maxHeight - 12);
        } else {
            $($allContent).children('a').each(function () {
                $div = $(this).children().children("div");
                $($div).height($height);
            });
            setTimeout(function () {
                sameHeight();
            }, 500);
        }
    }

    $("a.subItemClasse").click(function (e) {
        $("*").addClass("progressWait");
        hideCategoryIfOnPhone();
        showAjaxLoaderShop();
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
                    sameHeight();
                } else {
                    showAlertMessage(data);
                }
            });
    });

    $("a.subItemSetClasse").click(function (e) {
        $("*").addClass("progressWait");
        hideCategoryIfOnPhone();
        showAjaxLoaderShop();
        var target = e.target;
        if (!$(target).hasClass("clickable")) {
            target = $(target).parent().parent();
        }
        var $searchClass = $(target).attr("data-sub-class-id");
        var $id = $(target).attr('id');
        $.post("/api/shop/shop.php",
            {
                id: $id,
                searchClass: $searchClass
            },
            function (data, status) {
                $("*").removeClass("progressWait");
                hideAllHeaderShop();
                hideAjaxLoaderShop();
                if (data !== 'Error !' && data !== 'No Result !') {
                    showFilterShop();
                    $("#shopDisplayItems").html(data);
                    sameHeight();
                } else {
                    showAlertMessage(data);
                }
            });
    });

    $("#mainShopTitle").click(function (e) {
        loadHomePageShop();
    });

    $(document).ready(function () {
        $("#filterNameShop").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#filterNameListShop li").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        loadHomePageShop();
    });
</script>

<?php get_footer(); ?>

