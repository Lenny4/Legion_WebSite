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
    var sideBar = $('aside');
    var mainContent = $('main');
    var button = $("#showHideSideBar");
    if ($(sideBar).hasClass("active")) {
        $(sideBar).animate({
            left: '50%'
        }, 500);
        $(sideBar).removeClass("active");
        $(sideBar).addClass("hidden-xs");
        $(mainContent).removeClass("col-sm-8");
        $(mainContent).addClass("col-sm-10");
        $(button).removeClass("fa-arrow-circle-right");
        $(button).addClass("fa-arrow-circle-left");
    } else {
        $(sideBar).animate({
            left: '0%'
        }, 500);
        $(sideBar).removeClass("hidden-xs");
        $(sideBar).addClass("active");
        $(mainContent).removeClass("col-sm-10");
        $(mainContent).addClass("col-sm-8");
        $(button).removeClass("fa-arrow-circle-left");
        $(button).addClass("fa-arrow-circle-right");
    }
}

function addPlaceHolderForm() {
    $(document).ready(function () {
        $('#username').attr('placeholder', $('#username').prev().text().slice(0,-1));
        $('#user_email').attr('placeholder', $('#user_email').prev().text().slice(0,-1));
        $('#password').attr('placeholder', $('#password').prev().text().slice(0,-1));
    });
}

$(document).ready(function () {
    resizeVideo();
    addPlaceHolderForm();
});

$(window).resize(function () {
    resizeVideo();
});

$(document).scroll(function () {
    changeMenuCss()
});