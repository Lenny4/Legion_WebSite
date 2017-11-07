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
        $(sideBar).addClass("hidden-xs hidden-sm");
        $(mainContent).removeClass("col-md-8");
        $(mainContent).addClass("col-md-10");
        $(button).removeClass("fa-arrow-circle-right");
        $(button).addClass("fa-arrow-circle-left");
    } else {
        $(sideBar).animate({
            left: '0%'
        }, 500);
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

/*
    Pinterest Grid Plugin
    Copyright 2014 Mediademons
    @author smm 16/04/2014

    usage:

     $(document).ready(function() {

        $('#blog-landing').pinterest_grid({
            no_columns: 4
        });

    });


*/
;(function ($, window, document, undefined) {
    var pluginName = 'pinterest_grid',
        defaults = {
            padding_x: 10,
            padding_y: 10,
            no_columns: 3,
            margin_bottom: 50,
            single_column_breakpoint: 700
        },
        columns,
        $article,
        article_width;

    function Plugin(element, options) {
        this.element = element;
        this.options = $.extend({}, defaults, options) ;
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    Plugin.prototype.init = function () {
        var self = this,
            resize_finish;

        $(window).resize(function() {
            clearTimeout(resize_finish);
            resize_finish = setTimeout( function () {
                self.make_layout_change(self);
            }, 11);
        });

        self.make_layout_change(self);

        setTimeout(function() {
            $(window).resize();
        }, 500);
    };

    Plugin.prototype.calculate = function (single_column_mode) {
        var self = this,
            tallest = 0,
            row = 0,
            $container = $(this.element),
            container_width = $container.width();
        $article = $(this.element).children();

        if(single_column_mode === true) {
            article_width = $container.width() - self.options.padding_x;
        } else {
            article_width = ($container.width() - self.options.padding_x * self.options.no_columns) / self.options.no_columns;
        }

        $article.each(function() {
            $(this).css('width', article_width);
        });

        columns = self.options.no_columns;

        $article.each(function(index) {
            var current_column,
                left_out = 0,
                top = 0,
                $this = $(this),
                prevAll = $this.prevAll(),
                tallest = 0;

            if(single_column_mode === false) {
                current_column = (index % columns);
            } else {
                current_column = 0;
            }

            for(var t = 0; t < columns; t++) {
                $this.removeClass('c'+t);
            }

            if(index % columns === 0) {
                row++;
            }

            $this.addClass('c' + current_column);
            $this.addClass('r' + row);

            prevAll.each(function(index) {
                if($(this).hasClass('c' + current_column)) {
                    top += $(this).outerHeight() + self.options.padding_y;
                }
            });

            if(single_column_mode === true) {
                left_out = 0;
            } else {
                left_out = (index % columns) * (article_width + self.options.padding_x);
            }

            $this.css({
                'left': left_out,
                'top' : top
            });
        });

        this.tallest($container);
        $(window).resize();
    };

    Plugin.prototype.tallest = function (_container) {
        var column_heights = [],
            largest = 0;

        for(var z = 0; z < columns; z++) {
            var temp_height = 0;
            _container.find('.c'+z).each(function() {
                temp_height += $(this).outerHeight();
            });
            column_heights[z] = temp_height;
        }

        largest = Math.max.apply(Math, column_heights);
        _container.css('height', largest + (this.options.padding_y + this.options.margin_bottom));
    };

    Plugin.prototype.make_layout_change = function (_self) {
        if($(window).width() < _self.options.single_column_breakpoint) {
            _self.calculate(true);
        } else {
            _self.calculate(false);
        }
    };

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName,
                    new Plugin(this, options));
            }
        });
    }

})(jQuery, window, document);