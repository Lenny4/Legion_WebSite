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

    function __construct()
    {
        if (isset($GLOBALS["dbh"])) {
            $req = $GLOBALS["dbh"]->query("SELECT * FROM `item_home` WHERE `phpclasse`='" . get_class($this) . "'");
            while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
                $this->price = $data["price"];
            }
        }
    }

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
        return '<div class="col-sm-9 col-xs-12 alert alert-success">
  <strong>Coming soon!</strong>
</div>';
    }

    public function displayAllCharacters($allCharacters, $selectedCharacter = null, $notThisLevel = null)
    {
        $return = "";
        $notThisLevel = intval($notThisLevel);
        $characterRemoved = false;
        if (!empty($allCharacters) AND $notThisLevel != null) {
            foreach ($allCharacters as $key => $character) {
                if ($character["level"] == $notThisLevel) {
                    unset($allCharacters[$key]);
                    $characterRemoved = true;
                }
            }
        }
        if (!empty($allCharacters)) {
            $return .= ' <div class="form-group">
  <label for="select_character_' . get_class($this) . '">Select a character:</label>
  <select name="character_selected" data-phpClass="' . get_class($this) . '" class="form-control" id="select_character_item_home">';
            $addDefaultOption = true;
            foreach ($allCharacters as $character) {
                if ($selectedCharacter != null AND $character["name"] == $selectedCharacter) {
                    $return .= '<option selected value="' . $character["name"] . '">' . $character["name"] . ' lvl ' . $character["level"] . '</option>';
                    $addDefaultOption = false;
                } else {
                    $return .= '<option value="' . $character["name"] . '">' . $character["name"] . ' lvl ' . $character["level"] . '</option>';
                }
            }
            if ($addDefaultOption == true) {
                $return .= '<option disabled selected> -- Select a Character -- </option>';
            }
            $return .= '</select></div>';
        } else {
            if ($characterRemoved == true) {
                $return .= '<div class="alert alert-danger">
  <strong>You must create at least one character under lvl ' . $notThisLevel . '</strong>
</div>';
            } else {
                $return .= '<div class="alert alert-danger">
  <strong>You must create at least one character</strong>
</div>';
            }
        }
        return $return;
    }
}