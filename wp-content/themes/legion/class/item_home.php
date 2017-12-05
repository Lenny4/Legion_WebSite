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
include_once("item_home_gold.php");
include_once("item_home_character.php");
include_once("item_home_manage_character.php");
include_once("item_home_profession.php");
include_once("item_home_best_sell.php");
include_once("item_home_membership.php");
include_once("item_home_promotion.php");
include_once("item_home_guild.php");
include_once("item_home_teleport.php");

class item_home extends parent_item
{
    public $price = 0;
    public $promotion = 0;
    public $vote = 0;
    public $phpclasse = null;
    public $image = null;
    public $rank = 0;

    public function display()
    {
        $return = "";
        $req = $GLOBALS["dbh"]->query('SELECT * FROM `item_home` ORDER BY `rank` ASC');
        $allItems = array();
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $class = $data["phpclasse"];
            $object = new $class();
            $object->hydrateBDD($data);
            array_push($allItems, $object);
        }
        foreach ($allItems as $itemHome) {
            if ($itemHome->phpclasse == "item_home_profession") {
                $return .= "<div class='col-sm-9 col-xs-12 noPadding'>";
            }
            $return .= $itemHome->displayHome();
            if ($itemHome->phpclasse == "item_home_level") {
                $return .= "</div>";
            }
        }
        echo $return;
    }

    public function show()
    {
        return "Coming soon !";
    }

    public function displayAllCharacters($allCharacters)
    {
        $return = "";
        if (!empty($allCharacters)) {
            $return .= ' <div class="form-group">
  <label for="select_character_' . get_class($this) . '">Select a character:</label>
  <select class="form-control" id="select_character_' . get_class($this) . '">';
            $return .= '<option disabled selected> -- Select a Character -- </option>';
            foreach ($allCharacters as $character) {
                $return .= '<option value="' . $character["name"] . '">' . $character["name"] . ' lvl ' . $character["level"] . '</option>';
            }
            $return .= '</select></div>';
        } else {
            $return.='<div class="alert alert-danger">
  <strong>You must create at least one character to use the teleport</strong>
</div>';
        }
        return $return;
    }
}