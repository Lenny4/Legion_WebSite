<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 15/11/2017
 * Time: 20:26
 */

include_once("item_home.php");

class item_home_teleport extends item_home
{
    function displayHome()
    {
        $return = "";
        $return .= '
        <a class="pinterest" onclick="showMoreItemHome(\'' . get_class($this) . '\')">
            <li class="list-group-item col-sm-6 col-xs-12">
        <div class="display_item noPadding">
        ';
        $return .= wp_get_attachment_image($this->image, 'large', false, array("class" => "img-responsive center-block", "style" => "z-index:-1;width:90%;"));
        $return .= '<p class="message hidden-xs" style="width: 50%;background-image: url(\'' . wp_get_attachment_image_src(132, "full")[0] . '\') ">
                    <span>' . $this->name . '</span>
                    </p>
                    <p class="hidden-sm hidden-md hidden-lg Quickstyle text-center">' . $this->name . '</p>';
        $return .= '</div></li></a>';
        return $return;
    }

    public function show()
    {
        $return = "<div class='col-sm-9 col-xs-12'>";
        $allCharacters = $this->getCharacters();
        $return .= $this->displayAllCharacters($allCharacters);
        if (isWowAdmin()) {
            $return .= "<p>Add teleportation</p>";
        }
        $return .= "</div>";
        return $return;
    }
}