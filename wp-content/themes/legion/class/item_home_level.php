<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 15/11/2017
 * Time: 20:26
 */

include_once("item_home.php");

class item_home_level extends item_home
{
    function displayHome()
    {
        $return = "";
        $return .= '
        <a class="pinterest" onclick="showMoreItemHome(\'' . get_class($this) . '\')">
            <li class="list-group-item col-sm-5 col-xs-12">
        <div class="display_item noPadding">
        ';
        $return .= wp_get_attachment_image($this->image, 'large', false, array("class" => "img-responsive center-block", "style" => "z-index:-1;width:100%;"));
        $return .= '<p class="message hidden-xs" style="width: 60%;background-image: url(\'' . wp_get_attachment_image_src(314, "full")[0] . '\') ">
                    <span>' . $this->name . '</span>
                    </p>
                    <p class="hidden-sm hidden-md hidden-lg Quickstyle text-center">' . $this->name . '</p>';
        $return .= '</div></li></a>';
        return $return;
    }

    public function show()
    {
        echo "<div class='col-sm-9 col-xs-12'>";
        echo '<form id="changeLevelCharacterForm">' . $this->displayAllCharacters($this->getCharacters()) . '<div id="changeLevelCharacterFormContent"></div></form>';
        echo "</div>";
    }
}