<?php /* Template Name: Shop Page */ ?>
<?php get_header(); ?>

<style>
    #blog-landing {
        position: relative;
        max-width: 100%;
        width: 100%;
    }

    article img.product {
        width: 100%;
        max-width: 100%;
        height: auto;
        border-radius: 10px 10px 10px 10px;
    }

    .white-panel {
        position: absolute;
        background-color: rgb(28, 36, 23);
        box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.3);
        padding: 0px;
        border-radius: 10px 10px 10px 10px;
        display: inline-block;
    }

    .white-panel p {
        background-color: rgba(0, 0, 0, 0.7);
        font-size: 1em;
    }

    .white-panel:hover {
        box-shadow: 1px 1px 10px rgba(0, 0, 0, 0.5);
        margin-top: -5px;
        -webkit-transition: all 0.3s ease-in-out;
        -moz-transition: all 0.3s ease-in-out;
        -o-transition: all 0.3s ease-in-out;
        transition: all 0.3s ease-in-out;
    }

    .hovereffect {
        width: 100%;
        height: 100%;
        float: left;
        overflow: hidden;
        position: relative;
        text-align: center;
        cursor: default;
    }

    .hovereffect .overlay {
        width: 100%;
        height: 100%;
        position: absolute;
        overflow: hidden;
        left: 0;
        background-color: rgba(255, 255, 255, 0.7);
        top: -200px;
        opacity: 0;
        filter: alpha(opacity=0);
        -webkit-transition: all 0.1s ease-out 0.5s;
        transition: all 0.1s ease-out 0.5s;
        cursor: pointer;
        border-radius: 10px 10px 10px 10px;
    }

    .hovereffect:hover .overlay {
        opacity: 1;
        filter: alpha(opacity=100);
        top: 0px;
        -webkit-transition-delay: 0s;
        transition-delay: 0s;
    }

    .hovereffect img {
        display: block;
        position: relative;
    }

    .hovereffect h2 {
        text-transform: uppercase;
        color: #fff;
        text-align: center;
        position: relative;
        font-size: 17px;
        padding: 10px;
        background: rgba(0, 0, 0, 0.6);
        -webkit-transform: translateY(-200px);
        -ms-transform: translateY(-200px);
        transform: translateY(-200px);
        -webkit-transition: all ease-in-out 0.1s;
        transition: all ease-in-out 0.1s;
        -webkit-transition-delay: 0.3s;
        transition-delay: 0.3s;
    }

    .hovereffect:hover h2 {
        -webkit-transform: translateY(0px);
        -ms-transform: translateY(0px);
        transform: translateY(0px);
        -webkit-transition-delay: 0.3s;
        transition-delay: 0.3s;
    }

    .hovereffect a.info {
        display: inline-block;
        text-decoration: none;
        padding: 7px 14px;
        text-transform: uppercase;
        margin: 50px 0 0 0;
        background-color: transparent;
        -webkit-transform: translateY(-200px);
        -ms-transform: translateY(-200px);
        transform: translateY(-200px);
        color: #000;
        border: 1px solid #000;
        -webkit-transition: all ease-in-out 0.3s;
        transition: all ease-in-out 0.3s;
    }

    .hovereffect a.info:hover {
        box-shadow: 0 0 5px #fff;
    }

    .hovereffect:hover a.info {
        -webkit-transform: translateY(0px);
        -ms-transform: translateY(0px);
        transform: translateY(0px);
        box-shadow: 0 0 5px #000;
        color: #000;
        border: 1px solid #000;
        -webkit-transition-delay: 0.3s;
        transition-delay: 0.3s;
    }
</style>

<main class="col-xs-12 col-md-8 col-md-offset-1" role="main">
    <!-- section -->
    <section class="background" id="blog-landing">
        <article class="white-panel">
            <div class="hovereffect">
                <img class="product" src="http://www.mediademon.com/wp-content/uploads/2013/07/Blog-Post-Imagery5.png"
                     alt="ALT">
                <div class="overlay">
                    <h2>Hover effect 9</h2>
                </div>
            </div>
        </article>
        <article class="white-panel">
            <div class="hovereffect">
                <img class="product" src="http://www.mediademon.com/wp-content/uploads/2014/04/food-drink-expo.png"
                     alt="ALT">
                <div class="overlay">
                    <h2>Hover effect 9</h2>
                </div>
            </div>
        </article>
        <article class="white-panel">
            <div class="hovereffect">
                <img class="product" src="http://www.mediademon.com/wp-content/uploads/2014/03/tile-app-2.jpg"
                     alt="ALT">
                <div class="overlay">
                    <h2>Hover effect 9</h2>
                </div>
            </div>
        </article>
        <article class="white-panel">
            <div class="hovereffect">
                <img class="product" src="http://www.mediademon.com/wp-content/uploads/2014/03/tile-app-2.jpg"
                     alt="ALT">
                <div class="overlay">
                    <h2>Hover effect 9</h2>
                </div>
            </div>
        </article>
    </section>
    <!-- /section -->
</main>
<script src="/wp-content/themes/legion/js/pinterest_grid.js"></script>
<script>
    $(document).ready(function () {

        $('#blog-landing').pinterest_grid({
            no_columns: 3,
            padding_x: 10,
            padding_y: 10,
            margin_bottom: 50,
            single_column_breakpoint: 700
        });

    });
</script>
<?php get_sidebar(); ?>

<?php get_footer(); ?>
