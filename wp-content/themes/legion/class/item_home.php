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
        $return = "";
        $req = $GLOBALS["dbh"]->query('SELECT * FROM `item_home` ');
        $allItems = array();
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $class = $data["phpclasse"];
            $object = new $class();
            $object->hydrateBDD($data);
            array_push($allItems, $object);
        }
        foreach ($allItems as $itemHome) {
            $return .= $itemHome->displayHome();
        }
        echo $return;
    }
}