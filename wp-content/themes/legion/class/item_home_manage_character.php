<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 16/11/2017
 * Time: 19:54
 */

include_once("item_home.php");

class item_home_manage_character extends item_home
{
    function displayHome()
    {
        $return = "";
        $return .= '
        <a class="pinterest" onclick="showMoreItemHome(\'' . get_class($this) . '\')">
            <li class="list-group-item col-sm-3 col-xs-12">
        <div class="display_item noPadding">
        ';
        $return .= "<div class='heightMinPhone' style='width: 100%;height: 155px'></div>";
        $return .= wp_get_attachment_image($this->image, 'large', false, array("class" => "img-responsive center-block", "style" => "z-index:-1;height:100%;width:100%"));
        $return .= '<p class="message hidden-xs" style="top: 0%;width: 80%;background-image: url(\'' . wp_get_attachment_image_src(132, "full")[0] . '\') ">
                    <span>' . $this->name . '</span>
                    </p>
                    <p class="hidden-sm hidden-md hidden-lg Quickstyle text-center">' . $this->name . '</p>';
        $return .= '</div></li></a>';
        return $return;
    }
}