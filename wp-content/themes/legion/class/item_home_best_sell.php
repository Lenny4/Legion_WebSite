<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 15/11/2017
 * Time: 20:26
 */

include_once("item_home.php");

class item_home_best_sell extends item_home
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
        $return .= '<p class="message hidden-xs" style="width: 60%;background-image: url(\'' . wp_get_attachment_image_src(314, "full")[0] . '\') ">
                    <span>' . $this->name . '</span>
                    </p>
                    <p class="hidden-sm hidden-md hidden-lg Quickstyle text-center">' . $this->name . '</p>';
        $return .= '</div></li></a>';
        return $return;
    }

    public function show()
    {
        $req = $GLOBALS["dbh"]->query('SELECT DISTINCT `item_id` FROM `log_sells` WHERE `date` >= now() - interval 1 month AND `item_id`>0 AND `admin`=0 LIMIT 100');
        $count = $req->rowCount();
        if ($count > 0) {
            while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
                $item = createItem($data["item_id"]);
                $itemClass = createItemClass($item);
                echo $item->display($itemClass, true);
            }
        }
    }
}