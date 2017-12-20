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
        $return .= '<p class="message hidden-xs" style="width: 50%;background-image: url(\'' . wp_get_attachment_image_src(132, "full")[0] . '\') ">
                    <span>' . $this->name . '</span>
                    </p>
                    <p class="hidden-sm hidden-md hidden-lg Quickstyle text-center">' . $this->name . '</p>';
        $return .= '</div></li></a>';
        return $return;
    }

    function show()
    {
        echo "<div class='col-sm-9 col-xs-12'><form id='buy_gold' method='post'>";
        $all_characters = $this->getCharacters();
        echo $this->displayAllCharacters($all_characters);
        ?>
        <div id="displayInfoBuyGold">

        </div>
        <div class="form-group">
            <input name="amountOfGold" id="amountOfGold" type="text" data-slider-min="1000" data-slider-max="100000" data-slider-step="100" data-slider-value="2000"/>
            <span>Current Slider Value: <span id="amountOfGoldInfo">2000</span></span>
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
        <?php
        echo "</form></div>";
    }
}