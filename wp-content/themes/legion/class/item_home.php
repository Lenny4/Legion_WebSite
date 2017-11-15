<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 15/11/2017
 * Time: 20:23
 */

include_once("parent_item.php");

class item_home extends parent_item
{
    public $price = 0;
    public $promotion = 0;
    public $vote = 0;
    public $phpclasse = null;
    public $image = null;

    public function display()
    {
        var_dump($this);
    }
}