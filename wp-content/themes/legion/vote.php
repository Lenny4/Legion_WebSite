<?php /* Template Name: Vote Page */ ?>
<?php get_header(); ?>
<style>
    section.background {
        padding: 20px 0px;
    }
</style>
<main class="col-md-8 col-md-offset-1" role="main">
    <!-- section -->
    <section class="background">
        <?php if (serverOnline() == false AND (get_the_ID() == 16 OR get_the_ID() == 18)) { ?>
            <div class="alert alert-warning">
                <strong><?= get_field("information_update", 18); ?></strong>
            </div>
        <?php } ?>
        <h1><?php the_title(); ?></h1>
        <?php if (have_posts()): while (have_posts()) : the_post(); ?>
            <!-- article -->
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <div class="col-xs-12">
                    <?php the_content(); ?>
                    <?php $previousUrl = wp_get_referer(); ?>
                    <div class="row">
                        <?php
                        $result = $GLOBALS["dbh"]->query('SELECT * FROM `website_vote`');
                        $i = 0;
                        while ($data = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                            <?php
                            if (!parse_url($previousUrl, PHP_URL_HOST) == parse_url($data["url_vote"], PHP_URL_HOST)) {
                                $req = "SELECT * FROM `user_vote` WHERE `user_ip`='" . get_the_user_ip() . "' AND `website_id`=" . $data['id'] . " AND `status`='voting'";
                                $result2 = $GLOBALS["dbh"]->query($req);
                                while ($data2 = $result2->fetch(PDO::FETCH_ASSOC)) {
                                    $req = "UPDATE `user_vote` SET `status`='done',`date`= NOW() WHERE id=" . $data2['id'];
                                    $GLOBALS["dbh"]->query($req);
                                    //give points
                                }
                            }
                            ?>
                            <div class="col-sm-6 col-xs-12">
                                <div class="col-sm-3 col-xs-6">
                                    <?= wp_get_attachment_image($data["img"], "thumbnail", false, array("class" => "img-responsive")); ?>
                                </div>
                                <div class="col-sm-3 col-xs-6">
                                    <p class="h3 noMargin">
                                        <span style="float: left; margin-right: 1px"><?= $data["points"]; ?></span>
                                        <?= wp_get_attachment_image(169, "thumbnail", false, array("class" => "img-responsive", "style" => "width:24px")); ?>
                                    </p>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <p class="h3 noMargin">
                                        <span style="float: left; margin-right: 1px">This month <?= $data["points"]; ?></span>
                                        <?= wp_get_attachment_image(169, "thumbnail", false, array("class" => "img-responsive", "style" => "width:24px")); ?>
                                    </p>
                                </div>
                                <?php
                                if (true) { ?>
                                    <button data-href="<?= $data["url_vote"]; ?>" data-websiteid="<?= $data["id"]; ?>"
                                            type="button"
                                            class="btn btn-primary btn-block">Vote
                                    </button>
                                <?php } else { ?>
                                    <button type="button" class="btn btn-danger btn-block disabled">You can vote in 1h
                                        45min 23sec
                                    </button>
                                <?php } ?>
                            </div>
                            <?php $i++;
                            if ($i == 2) {
                                echo "<div class='col-xs-12 hidden-xs' style='height: 30px'></div>";
                            }
                        } ?>
                    </div>
                    <?php
                    if (isset($_POST["add_voting_website"]) AND isWowAdmin()) {
                        $req = "INSERT INTO `website_vote`(`url_vote`, `img`, `points`, `times`) VALUES ('" . $_POST['url'] . "'," . $_POST['image_id'] . "," . $_POST['points'] . "," . $_POST['times'] . ")";
                        $GLOBALS["dbh"]->query($req);
                    }
                    ?>
                    <?php if (isWowAdmin()) { ?>
                        <br style="clear: both">
                        <hr>
                        <div class="col-xs-12">
                            <button class="btn btn-primary btn-block" data-toggle="collapse" data-target="#adminVote">
                                Admin
                            </button>
                            <div id="adminVote" class="collapse">
                                <div class="col-sm-5 col-xs-12">
                                    <form method="post">
                                        <h2>Add website</h2>
                                        <div class="form-group">
                                            <label for="url">Url the voting site gave you</label>
                                            <input type="text" class="form-control" name="url" id="url">
                                        </div>
                                        <div class="form-group">
                                            <label for="image_id">Id the image of the website</label>
                                            <input type="number" class="form-control" name="image_id" id="image_id">
                                        </div>
                                        <div class="form-group">
                                            <label for="points">How much points the user win ?</label>
                                            <input type="number" class="form-control" name="points" id="points">
                                        </div>
                                        <div class="form-group">
                                            <label for="times">Time in seconds, between each vote</label>
                                            <input type="number" class="form-control" name="times" id="times">
                                        </div>
                                        <button name="add_voting_website" type="submit" class="btn btn-default">Submit
                                        </button>
                                    </form>
                                </div>
                                <div class="col-sm-5 col-sm-offset-1 col-xs-12">
                                    <h2>Remove website</h2>
                                </div>
                            </div>
                        </div>

                    <?php } ?>
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
    function voteSite($url, $websiteid, $btn) {
        $.post("/api/vote/vote.php",
            {
                id: "vote",
                websiteid: $websiteid
            },
            function (data) {
                $($btn).button('reset');
                if (data === "vote") {
                    window.location.href = $url;
                } else if (data === "is_voting") {
                    alert("display message and redirect");
                } else {
                    alert("error");
                }
            });
    }

    $(document).ready(function () {
        $("button[data-href]").click(function () {
            $(this).button('loading');
            var $url = $(this).data("href");
            var $websiteid = $(this).data("websiteid");
            voteSite($url, $websiteid, this);
        });
    });
</script>

<?php get_footer(); ?>
