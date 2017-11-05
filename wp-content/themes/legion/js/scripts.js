function resizeVideo()
{
    var video=$('header').find('video');
    var initialLeftVideo=-340;
    var initialWidthWindow=1349;
    var widthWindow=$(window).width();
    var diff=initialWidthWindow-widthWindow;
    $(video).css('left',(initialLeftVideo-(diff/2)));
}

function changeMenuCss()
{
    var scrollTop = $(window).scrollTop();
    var nav=$('header').find('nav');
    var liA=$('#navbar').children('ul.navbar-nav').children('li').children('a');
    var logo=$(nav).find("img.mainLogoHomePage");
    if (scrollTop > 10) {
        $(nav).css('background-color','rgba(28,36,23,1)');
        $(liA).css('padding-top','15px');
        $(liA).css('padding-bottom','15px');
        $(logo).addClass('smallLogo');
    } else if (scrollTop < 10) {
        $(nav).css('background-color','rgba(0,0,0,0.4)');
        $(logo).removeClass('smallLogo');
        if($(window).width()<=768){
            $(liA).css('padding-top','15px');
            $(liA).css('padding-bottom','15px');
        }else{
            $(liA).css('padding-top','40px');
            $(liA).css('padding-bottom','40px');
        }
    }
}

$( document ).ready(function() {
    resizeVideo();
});

$( window ).resize(function() {
    resizeVideo();
});

$(document).scroll(function () {
    changeMenuCss()
});