function resizeVideo() {
    var video = $('header').find('video');
    var initialLeftVideo = -320;
    var initialWidthWindow = 1349;
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

function hideShowSideBar() {
    var time = 500;
    var sideBar = $('aside');
    var mainContent = $('main');
    var button = $("#showHideSideBar");
    if ($(sideBar).hasClass("active")) {
        $(sideBar).animate({
            left: '50%'
        }, time, function () {
            $(sideBar).removeClass("active");
            $(sideBar).addClass("hidden-xs hidden-sm");
            $(mainContent).removeClass("col-md-8");
            $(mainContent).addClass("col-md-10");
            $(button).removeClass("fa-arrow-circle-right");
            $(button).addClass("fa-arrow-circle-left");
        });

    } else {
        $(sideBar).animate({
            left: '0%'
        }, time);
        $(sideBar).removeClass("hidden-xs hidden-sm");
        $(sideBar).addClass("active");
        $(mainContent).removeClass("col-md-10");
        $(mainContent).addClass("col-md-8");
        $(button).removeClass("fa-arrow-circle-left");
        $(button).addClass("fa-arrow-circle-right");
    }
}

function addPlaceHolderForm() {
    $(document).ready(function () {
        $('#username').attr('placeholder', $('#username').prev().text().slice(0, -1));
        $('#user_email').attr('placeholder', $('#user_email').prev().text().slice(0, -1));
        $('#password').attr('placeholder', $('#password').prev().text().slice(0, -1));
    });
}

function previewItem(data) {
    var modal = $('#shopAdminModal');
    $(modal).modal('show');
    var modalHeader = $(modal).find('.modal-title');
    var modalContent = $(modal).find('.modal-body');
    $(modalHeader).html("Add item");
    $(modalContent).html(data);
    if (data !== '<div class="alert alert-danger"><strong>Not Found</strong></div>') {
        $(modalContent).append("<button onclick='addItem(this)' id='addItem' class='btn btn-default'>Add to the shop</button>");
    }
}

function addItem(button) {
    var form = 'id=' + $(button).attr("id") + "&" + $("#previewItem").serialize();
    $.post("/api/shop/shop.php", form, function (data, status) {
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
    if (data != '<div class="alert alert-danger"><strong>Not Found</strong></div>') {
        $(modalContent).append("<button onclick='addItemSet(this)' id='addItemSet' class='btn btn-default'>Add to the shop</button>");
    }
}

function addItemSet(button) {
    var form = 'id=' + $(button).attr("id") + "&" + $("#previewItemSet").serialize();
    $.post("/api/shop/shop.php", form, function (data, status) {
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

$(document).ready(function () {
    resizeVideo();
    addPlaceHolderForm();
    //Shop Admin
    $("form").submit(function (event) {
        event.preventDefault();
        var form = 'id=' + $(event.target).attr("id") + "&" + $(event.target).serialize();
        $("*").addClass("progressWait");
        $.post("/api/shop/shop.php", form, function (data, status) {
            $("*").removeClass("progressWait");
            if (status === "success") {
                if ($(event.target).attr("id") === "previewItem") {
                    previewItem(data);
                }
                if ($(event.target).attr("id") === "previewItemSet") {
                    previewItemSet(data);
                }
            }
        });
    });
});

$(window).resize(function () {
    resizeVideo();
});

$(document).scroll(function () {
    changeMenuCss()
});