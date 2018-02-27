//====================== GLOBAL

function resizeVideo() {
    var video = $('header').find('video');
    var initialLeftVideo = -150;
    var initialWidthWindow = 1349;//sert de repérage par rapport a la fenêtre qui a permit le cadrage
    var widthWindow = $(window).width();
    var diff = initialWidthWindow - widthWindow;
    $(video).css('left', (initialLeftVideo - (diff / 2)));
}

function changeMenuCss() {
    var scrollTop = $(window).scrollTop();
    var nav = $('header').find('nav');
    var liA = $('#navbar').children('ul.navbar-nav').children('li').children('a');
    var logo = $(nav).find("img.mainLogoHomePage");
    if (scrollTop > 10) {
        $(nav).css('background-color', 'rgba(28,36,23,1)');
        $(liA).css('padding-top', '15px');
        $(liA).css('padding-bottom', '15px');
        $(logo).addClass('smallLogo');
    } else if (scrollTop < 10) {
        $(nav).css('background-color', 'rgba(0,0,0,0.4)');
        $(logo).removeClass('smallLogo');
        if ($(window).width() <= 768) {
            $(liA).css('padding-top', '15px');
            $(liA).css('padding-bottom', '15px');
        } else {
            $(liA).css('padding-top', '40px');
            $(liA).css('padding-bottom', '40px');
        }
    }
}

function hideShowSideBar(resize) {
    var time = 500;
    var sideBar = $('aside');
    var button = $("#showHideSideBar");
    if (resize === true) {
        if ($(window).width() > 992 && $(sideBar).css("left") !== "0px") {
            $(sideBar).removeClass("hidden-xs hidden-sm");
            $(sideBar).addClass("active");
        }
    } else {
        if ($(sideBar).hasClass("active")) {
            animateRotate(button, 360);
            $(sideBar).animate({
                left: '80%'
            }, time, function () {
                $(sideBar).removeClass("active");
                $(sideBar).addClass("hidden-xs hidden-sm");
            });

        } else {
            animateRotate(button, 360);
            $(sideBar).animate({
                left: '0%'
            }, time);
            $(sideBar).removeClass("hidden-xs hidden-sm");
            $(sideBar).addClass("active");
            $(button).removeClass("fa-arrow-circle-left");
            $(button).addClass("fa-arrow-circle-right");
        }
    }
}

function maxHeightSideBar() {
    var $heightNav = $("header nav").height();
    var $heightSideBar = $(window).height() - $heightNav;
    var heightTopTop = $("header").height() - $(window).scrollTop();
    var $footerHeight = $("footer").height();
    $footerHeight = $(document).height() - $footerHeight;
    var diffHeight = $(window).scrollTop() + $(window).height() - $footerHeight;
    if ($(window).width() > 992) {
        $("#my_sidebar").css({position: 'fixed', right: '5%', left: 'auto'});
        if (heightTopTop < 0) {
            $("#my_sidebar").css({top: $heightNav - 10 + 'px'});
        } else {
            $("#my_sidebar").css({top: heightTopTop + 'px'});
        }
        var $maxValue = $(document).height() - $("header").height() - $("footer").height() - 15;
        if ($maxValue < $("#my_sidebar").height()) {
            $("#my_sidebar").css("max-height", $maxValue + "px");
        } else {
            if (diffHeight > 0 && $maxValue > $("#my_sidebar").height()) {
                $("#my_sidebar").css("max-height", $heightSideBar - diffHeight - 5 + "px");
            } else if ($maxValue > $("#my_sidebar").height()) {
                $("#my_sidebar").css("max-height", $heightSideBar + "px");
            }
        }
    } else {
        $("#my_sidebar").css("max-height", $(window).height() + "px");
        $("#my_sidebar").css({position: 'inherit', top: "0px"});
    }
}

function addPlaceHolderForm() {
    $(document).ready(function () {
        $('#username').attr('placeholder', $('#username').prev().text().slice(0, -1));
        $('#user_email').attr('placeholder', $('#user_email').prev().text().slice(0, -1));
        $('#password').attr('placeholder', $('#password').prev().text().slice(0, -1));
    });
}

function animateRotate($elem, angle) {
    $({deg: 0}).animate({deg: angle}, {
        duration: 500,
        step: function (now) {
            $($elem).css({
                transform: 'rotate(' + now + 'deg)'
            });
        }
    });
}

//====================== GLOBAL

//====================== SHOP ADMIN

function previewItem(data) {
    var modal = $('#shopAdminModal');
    $(modal).modal('show');
    var modalHeader = $(modal).find('.modal-title');
    var modalContent = $(modal).find('.modal-body');
    $(modalHeader).html("Add item");
    $(modalContent).html(data);
    if (data !== '<div class="alert alert-danger"><strong>Not Found !</strong></div>') {
        $(modalContent).append("<button onclick='addItem(this)' id='addItem' class='btn btn-default'>Add to the shop</button>");
    }
}

function addItem(button) {
    $("*").addClass("progressWait");
    var form = 'id=' + $(button).attr("id") + "&" + $("#previewItem").serialize();
    $.post("/api/shop/shop.php", form, function (data, status) {
        $("*").removeClass("progressWait");
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

function previewItemSet(data) {
    var modal = $('#shopAdminModal');
    $(modal).modal('show');
    var modalHeader = $(modal).find('.modal-title');
    var modalContent = $(modal).find('.modal-body');
    $(modalHeader).html("Add item set");
    $(modalContent).html(data);
    if (data != '<div class="alert alert-danger"><strong>Not Found !</strong></div>') {
        $(modalContent).append("<button onclick='addItemSet(this)' id='addItemSet' class='btn btn-default'>Add to the shop</button>");
    }
}

function addItemSet(button) {
    $("*").addClass("progressWait");
    var form = 'id=' + $(button).attr("id") + "&" + $("#previewItemSet").serialize();
    $.post("/api/shop/shop.php", form, function (data, status) {
        $("*").removeClass("progressWait");
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

function addAllItem($minId, $maxId, $idPOST, $currentId) {
    $currentId = parseInt($currentId);
    $maxId = parseInt($maxId);
    var pourcent = (($currentId - $minId) / ($maxId - $minId)) * 100;
    $.post("/api/shop/shop.php",
        {
            id: $idPOST,
            currentId: $currentId
        },
        function (data, status) {
            $("#pourcentAllItems").html(pourcent + "%" + " / id=" + $currentId + data);
            if ($currentId < $maxId) {
                addAllItem($minId, $maxId, $idPOST, $currentId + 1)
            } else {
                $("*").removeClass("progressWait");
            }
        });
}

function addAllItemSet($minId, $maxId, $idPOST, $currentId) {
    $currentId = parseInt($currentId);
    $maxId = parseInt($maxId);
    var pourcent = (($currentId - $minId) / ($maxId - $minId)) * 100;
    $.post("/api/shop/shop.php",
        {
            id: $idPOST,
            currentId: $currentId
        },
        function (data, status) {
            $("#pourcentAllItemsSet").html(pourcent + "%" + " / id=" + $currentId + data);
            if ($currentId < $maxId) {
                addAllItemSet($minId, $maxId, $idPOST, $currentId + 1)
            } else {
                $("*").removeClass("progressWait");
            }
        });
}

function askedItemAdded($id, $button) {
    $($button).parent().remove();
    $.post("/api/shop/shop.php",
        {
            id: "askedItemAdded",
            item_id: $id
        },
        function (data, status) {
        });
}

function askedItemRefused($id, $button) {
    $($button).parent().remove();
    $.post("/api/shop/shop.php",
        {
            id: "askedItemRefused",
            item_id: $id
        },
        function (data, status) {
        });
}

function askedItemSetAdded($id, $button) {
    $($button).parent().remove();
    $.post("/api/shop/shop.php",
        {
            id: "askedItemSetAdded",
            item_set_id: $id
        },
        function (data, status) {
        });
}

function askedItemSetRefused($id, $button) {
    $($button).parent().remove();
    $.post("/api/shop/shop.php",
        {
            id: "askedItemSetRefused",
            item_set_id: $id
        },
        function (data, status) {
        });
}

function deleteMessageHeader($id) {
    $.post("/api/shop/shop.php",
        {
            id: "deleteMessageHeader",
            messageId: $id
        },
        function (data, status) {
        });
}

function scrollTopDocument() {
    var body = $("html, body");
    body.stop().animate({scrollTop: 0}, 300, 'swing', function () {
    });
}

//====================== SHOP ADMIN

//====================== SHOP

function removeItemCart($this, $id, $type) {
    $($this).parent().parent().remove();
    var count = $("#item_cart").children().length;
    if (count === 0) {
        $("#cart_shop").collapse("hide");
        $("#cart_shop").css('display', 'none');
    }
    $.post("/api/shop/shop.php",
        {
            id: "removeToCart",
            item_item_set_id: $id,
            type: $type
        },
        function (data, status) {
        });
}

function countDownJs() {
    $("span[data-countdown]").each(function () {
        countDown(this, $(this).data("countdown"));
    });
}

function countDown($this, $time) {
    setInterval(function () {
        var tempsEnMs = (parseInt(Date.now() / 1000) - $time) * -1;
        var hours = parseInt(tempsEnMs / 3600);
        tempsEnMs = tempsEnMs - (hours * 3600);
        var minutes = parseInt(tempsEnMs / 60);
        var seconds = parseInt(tempsEnMs - (minutes * 60));
        if (hours > 0) {
            $($this).html(hours + "h " + minutes + "m " + seconds + "s ");
        } else if (minutes > 0) {
            $($this).html(minutes + "m " + seconds + "s ");
        } else if (seconds > 0) {
            $($this).html(seconds + "s ");
        } else {
            $($this).html("Please reload the page to vote");
        }
    }, 1000);
}

//====================== SHOP

$(document).ready(function () {
    resizeVideo();
    addPlaceHolderForm();
    maxHeightSideBar();
    $("a.deleteMessageHeader").click(function () {
        deleteMessageHeader($(this).attr('id'));
    });
    new PerfectScrollbar('#my_sidebar');
    countDownJs();
});

$(window).resize(function () {
    resizeVideo();
    hideShowSideBar(true);
    maxHeightSideBar();
});

$(document).scroll(function () {
    changeMenuCss();
    maxHeightSideBar();
});