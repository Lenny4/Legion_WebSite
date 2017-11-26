<?php /* Template Name: Shop Page */ ?>
<?php get_header(); ?>
<?php $askNewItemsText = get_field("how_to_add_new_items", get_the_ID()); ?>

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
                <p data-toggle="collapse" data-target="#search_item_item_set" class="clickable text-center h4 overGreen"
                   style="font-family: inherit;">
                    Search a specific item ?
                </p>
                <div id="search_item_item_set" class="col-xs-12 collapse">
                    <div class="col-sm-6 col-xs-12 text-center">
                        <a class="h4" style="font-family: inherit;color: #337ab7" target="_blank"
                           href="http://www.wowhead.com/database">Find items ids</a>
                        <form id="searchItem">
                            <div class="form-group">
                                <input placeholder="Search item by ID" type="number" class="form-control"
                                       name="search_item_id">
                            </div>
                            <div class="form-group">
                                <input placeholder="Search item by name" type="text" class="form-control"
                                       name="search_item_name">
                            </div>
                            <button type="submit" class="btn btn-default">Search</button>
                        </form>
                    </div>
                    <div class="col-xs-12 hidden-lg hidden-md hidden-sm">
                        <hr/>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <p class="h4 text-center" style="font-family: inherit">Can't find an item in our database
                            ?</p>
                        <p class="text-center" style="font-family: inherit">
                            <button onclick="askNewItems()" type="button" class="btn btn-default"> Let us know</button>
                        </p>
                    </div>
                </div>
                <p> Automatic translation :</p><?php echo do_shortcode('[gtranslate]'); ?>
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
                                            <a data-toggle="collapse" data-parent="#accordion"
                                               href="#setofitemcollapse"
                                               class="collapsed" aria-expanded="false">Set of items</a>
                                        </h4>
                                    </div>
                                    <div id="setofitemcollapse" class="panel-collapse collapse"
                                         aria-expanded="false"
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
                            <div style="display: none" id="filterShop">
                                <input class="form-control" id="filterShopInput" type="text" placeholder="Filter">
                            </div>
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

    var $dontExecuteHeightShop = true;

    function showFilterShop() {
        $("#filterShop").show();
    }

    function showAlertMessage(data) {
        $("#shopDisplayError").show();
        $("#shopDisplayItems").html("");
        $("#shopDisplayError").html('<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>' + data + '</strong></div>');
    }

    function hideAllHeaderShop() {
        $("#filterShop").hide();
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
        if ($(window).width() <= 768 && $("#accordion").hasClass("hidden-xs") === false) {
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
        $("*").addClass("progressWait");
        hideCategoryIfOnPhone();
        showAjaxLoaderShop();
        var $id = "showHomeItems";
        $.post("/api/shop/shop.php",
            {
                id: $id
            },
            function (data, status) {
                $("*").removeClass("progressWait");
                hideAllHeaderShop();
                hideAjaxLoaderShop();
                $("#shopDisplayItems").html(data);
                $dontExecuteHeightShop = true;
            });
    }

    function sameHeight() {
        var $currentPosition = null;
        var $element1 = null;
        var $element2 = null;
        var $element3 = null;
        var $elementPos1 = null;
        var $elementPos2 = null;
        var $elementPos3 = null;
        if ($dontExecuteHeightShop === false && $(window).width() > 768) {
            $('a.pinterest').each(function (i, obj) {
                $obj = $(obj).children("li").children("div.display_item_small");
                var position = $($obj).offset();
                if ($currentPosition !== position.top && $currentPosition !== null) {//change line
                    var newHeight = $($element1).height();
                    if ($elementPos3 === null && $elementPos1 !== null && $elementPos2 !== null) {//2 items on the line
                        console.log(1);
                        if ($($element2).height() > newHeight) {
                            newHeight = $($element2).height();
                        }
                        $($element1).height(newHeight);
                        $($element2).height(newHeight);
                    } else if ($elementPos3 !== null && $elementPos1 !== null && $elementPos2 !== null) {//3 items on the line
                        if ($($element2).height() > newHeight) {
                            newHeight = $($element2).height();
                        }
                        if ($($element3).height() > newHeight) {
                            newHeight = $($element3).height();
                        }
                        $($element1).height(newHeight);
                        $($element2).height(newHeight);
                        $($element3).height(newHeight);
                    }
                    $currentPosition = null;
                    $element1 = null;
                    $element2 = null;
                    $element3 = null;
                    $elementPos1 = null;
                    $elementPos2 = null;
                    $elementPos3 = null;
                }
                if ($currentPosition === null) {
                    $currentPosition = position.top;
                }
                if ($element1 === null) {
                    $element1 = $obj;
                }
                else if ($element2 === null) {
                    $element2 = $obj;
                }
                else if ($element3 === null) {
                    $element3 = $obj;
                }
                if ($elementPos1 === null) {
                    $elementPos1 = position.top;
                }
                else if ($elementPos2 === null) {
                    $elementPos2 = position.top;
                }
                else if ($elementPos3 === null) {
                    $elementPos3 = position.top;
                }
            });
        }
        if ($(window).width() <= 768) {
            $("div.display_item").height("auto");
        }
        setTimeout(function () {
            sameHeight();
            return false;
        }, 800);
    }

    function showMoreItemGlobal($subClassId, $classId, $lastItemId) {
        if ($subClassId < 0 || $classId < 0 || $lastItemId <= 0) {
            return false;
        } else {
            $("*").addClass("progressWait");
            $("#showMoreItemGlobal").html($("#ajaxLoaderShop").html());
            $.post("/api/shop/shop.php",
                {
                    id: 'subItemClasse',
                    subClassId: $subClassId,
                    classId: $classId,
                    lastItemId: $lastItemId
                },
                function (data, status) {
                    $("*").removeClass("progressWait");
                    $("#showMoreItemGlobal").remove();
                    $("#shopDisplayItems").append(data);
                });
        }
    }

    function filterShop() {
        var value = $("#filterShopInput").val().toLowerCase();
        $("#filterNameListShop li").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
        setTimeout(function () {
            filterShop();
        }, 300);
    }

    function askNewItems() {
        var textExplain = <?php echo json_encode($askNewItemsText); ?>;
        var modal = $('#shopModal');
        $(modal).modal('show');
        var modalHeader = $(modal).find('.modal-title');
        var modalContent = $(modal).find('.modal-body');
        $(modalHeader).html("");
        $(modalContent).html('' +
            '<div class="col-xs-12">\n' +
            '<p class="pointer overGreen h3 text-center" style="font-family:inherit" data-toggle="collapse" data-target="#howToAddItem">How to add new items ?</p>\n' +
            '            <div id="howToAddItem" style="color:white" class="collapse">\n' +
            textExplain +
            '                </div>' +
            '                    <form id="customer_add_items" style="margin-bottom:10px;" method="post">\n' +
            '                        <div class="col-sm-6 col-xs-12">\n' +
            '                            <p class="h4 text-center" style="font-family: inherit">Add new item</p>\n' +
            '                            <div class="form-group">\n' +
            '                                <input placeholder="IDs" type="text" class="form-control"\n' +
            '                                       name="items_id">\n' +
            '                            </div>\n' +
            '                        </div>\n' +
            '                        <div class="col-sm-6 col-xs-12">\n' +
            '                            <p class="h4 text-center" style="font-family: inherit">Add new item set</p>\n' +
            '                            <div class="form-group">\n' +
            '                                <input placeholder="IDs" type="text" class="form-control"\n' +
            '                                       name="items_set_id">\n' +
            '                            </div>\n' +
            '                        </div>\n' +
            '                        <p class="text-center">\n' +
            '                            <button type="submit" class="btn btn-default">Ask to add these items</button>\n' +
            '                        </p>\n' +
            '                    </form>\n' +
            '                    <div id="customer_add_items_result"></div>\n' +
            '                </div>' +
            ''
        )
        ;
    }

    function showMoreItemHome($phpClasse) {
        console.log($phpClasse);
    }

    $(document).ready(function () {
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
                    $dontExecuteHeightShop = false;
                    $("*").removeClass("progressWait");
                    hideAllHeaderShop();
                    hideAjaxLoaderShop();
                    if (data !== 'Error !' && data !== 'No Result !') {
                        showFilterShop();
                        $("#shopDisplayItems").html(data);
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
                    $dontExecuteHeightShop = false;
                    $("*").removeClass("progressWait");
                    hideAllHeaderShop();
                    hideAjaxLoaderShop();
                    if (data !== 'Error !' && data !== 'No Result !') {
                        $("#shopDisplayItems").html(data);
                    } else {
                        showAlertMessage(data);
                    }
                });
        });

        $("#mainShopTitle").click(function (e) {
            loadHomePageShop();
        });


        $('body').on('submit', 'form', function (event) {
            if ($(event.target).attr("id") === "wpum_loginform") {
                return;
            }
            event.preventDefault();
            $("*").addClass("progressWait");
            if ($(event.target).attr("id") !== "customer_add_items") {
                hideCategoryIfOnPhone();
                showAjaxLoaderShop();
            } else {
                $("#customer_add_items_result").html($("#ajaxLoaderShop").html());
            }
            var form = 'id=' + $(event.target).attr("id") + "&" + $(event.target).serialize();
            $.post("/api/shop/shop.php", form, function (data, status) {
                $("*").removeClass("progressWait");
                if ($(event.target).attr("id") === "customer_add_items") {
                    $("#customer_add_items_result").html(data);
                } else {
                    hideAllHeaderShop();
                    hideAjaxLoaderShop();
                    if (data !== 'Error !' && data !== 'No Result !') {
                        $dontExecuteHeightShop = false;
                        $("#shopDisplayItems").html(data);
                    } else {
                        showAlertMessage(data);
                    }
                }
            });
        });
        loadHomePageShop();
        sameHeight();
        filterShop();
    });

</script>

<?php get_footer(); ?>

