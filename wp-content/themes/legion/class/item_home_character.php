<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 16/11/2017
 * Time: 19:54
 */

include_once("item_home.php");

class item_home_character extends item_home
{
    function displayHome()
    {
        $return = "";
        $return .= '
        <a class="pinterest" onclick="showMoreItemHome(\'' . get_class($this) . '\')">
            <li class="list-group-item col-sm-9 col-xs-12" style="padding-top:0px;">
        <div class="display_item noPadding">
        ';
        $return .= wp_get_attachment_image($this->image, 'large', false, array("class" => "img-responsive center-block", "style" => "z-index:-1;width:60%;margin-left: 0;"));
        $return .= '<p class="message hidden-xs" style="left: 300px;top: 60px;width: 50%;background-image: url(\'' . wp_get_attachment_image_src(314, "full")[0] . '\') ">
                    <span>' . $this->name . '</span>
                    </p>
                    <p class="hidden-sm hidden-md hidden-lg Quickstyle text-center">' . $this->name . '</p>';
        $return .= '</div></li></a>';
        return $return;
    }

    function show()
    {
        echo "<div class='col-sm-9 col-xs-12'><form id='buy_character' method='post'>";
        $all_characters = $this->getCharacters();
        echo $this->displayAllCharacters($all_characters, null, 110);
        echo "<div id='formContentCharacter' data-toggle=\"buttons\"></div>";
        echo "<button style='display: none' type=\"submit\" class=\"btn btn-primary btn-block\">Buy !</button></form>
<div id='resultBuyCharacter'></div></div>";
    }
}