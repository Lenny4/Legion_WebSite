<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 15/11/2017
 * Time: 20:23
 */

include_once("parent_item.php");
include_once("item_home_level.php");
include_once("item_home_transmo.php");

class item_home extends parent_item
{
    public $price = 0;
    public $promotion = 0;
    public $vote = 0;
    public $phpclasse = null;
    public $image = null;

    public function display()
    {
        $item_home_level = new item_home_level();
        $item_home_trasmo = new item_home_transmo();
        $return = "";
        $return .= $item_home_level->displayHome() . $item_home_trasmo->displayHome();
        echo $return;
    }
}