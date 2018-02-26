<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 15/11/2017
 * Time: 20:26
 */

include_once("item_home.php");

class item_home_gold extends item_home
{
    function displayHome()
    {
        $return = "";
        $return .= '
        <a class="pinterest" onclick="showMoreItemHome(\'' . get_class($this) . '\')">
            <li class="list-group-item col-sm-4 col-xs-12">
        <div class="display_item noPadding">
        ';
        $return .= wp_get_attachment_image($this->image, 'large', false, array("class" => "img-responsive center-block", "style" => "z-index:-1;width:100%;"));
        $return .= '<p class="message hidden-xs" style="width: 50%;background-image: url(\'' . wp_get_attachment_image_src(314, "full")[0] . '\') ">
                    <span>' . $this->name . '</span>
                    </p>
                    <p class="hidden-sm hidden-md hidden-lg Quickstyle text-center">' . $this->name . '</p>';
        $return .= '</div></li></a>';
        return $return;
    }

    function show()
    {
        $all_characters = $this->getCharacters();
        ?>
        <div class='col-sm-9 col-xs-12'>
            <div id="buyGoldAlert"></div>
            <div class="form-group">
                <label for="specificAmountOfGold">Specific amount of gold:</label>
                <input type="number" min="<?= MIN_AMOUNT_OF_GOLD_BUY; ?>" max="<?= MIN_AMOUNT_OF_GOLD_BUY; ?>"
                       class="form-control" id="specificAmountOfGold">
            </div>
            <form id='buy_gold' method='post'>
                <?= $this->displayAllCharacters($all_characters); ?>
                <div class="form-group">
                    <input name="amountOfGold" id="amountOfGold" type="text"
                           data-slider-min="<?= MIN_AMOUNT_OF_GOLD_BUY; ?>"
                           data-slider-max="<?= MAX_AMOUNT_OF_GOLD_BUY; ?>"
                           data-slider-step="100" data-slider-value="<?= MIN_AMOUNT_OF_GOLD_BUY; ?>"/>
                </div>
                <div class="row" style="margin: 15px 0px">
                    <div class="center-block" style="display: table">
                        <?= wp_get_attachment_image(119, 'full', false, ["class" => "img-responsive", "style" => "float:left;position: relative;top: 4px;"]) ?>
                        <span id="amountOfGoldInfo" style="margin-left: 10px"></span>
                    </div>
                    <div class="col-xs-6">
                        <div class="col-sm-4 col-sm-offset-8 col-xs-6 text-center">
                            <?= wp_get_attachment_image(168, 'full', false, ["class" => "img-responsive center-block"]) ?>
                            <p class="text-center" style="color: red" id="linkGoldBuyPointDel"></p>
                            <p class="text-center" id="linkGoldBuyPoint"></p>
                            <input value="buy" checked type="radio" name="radio_item_home_gold">
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="col-sm-4 col-xs-6 text-center">
                            <?= wp_get_attachment_image(169, 'full', false, ["class" => "img-responsive center-block"]) ?>
                            <p class="text-center" style="color: red" id="linkGoldVotePointDel"></p>
                            <p class="text-center" id="linkGoldVotePoint"></p>
                            <input value="vote" checked type="radio" name="radio_item_home_gold">
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <p class="h3 text-center" style="font-family: inherit">Reduction : <span
                                    id="reductionItemHomeGold"></span>%</p>
                    </div>
                </div>
                <?php if (!empty($all_characters)) { ?>
                    <button type="submit" class="btn btn-primary btn-block">Give me my money !</button>
                <?php } ?>
            </form>
        </div>
        <?php
    }
}