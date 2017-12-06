<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 05/12/2017
 * Time: 18:16
 */

include_once("item_home.php");

class map
{
    private $id;
    public $name;
    public $url;
    public $parent;
    private $children;
    public $topPos;
    public $leftPos;
    public $width;
    public $height;
    public $isCity;
    public $minLevel;
    public $maxLevel;
    public $canTp;
    public $radius;
    public $rotate;

    function __construct()
    {
        $this->initialiseNewMap();
    }

    private function initialiseNewMap()
    {
        $this->id = null;
        $this->name = null;
        $this->url = null;
        $this->children = array();
        $this->parent = null;
        $this->topPos = 0;
        $this->leftPos = 0;
        $this->width = 10;
        $this->height = 10;
        $this->isCity = null;
        $this->minLevel = null;
        $this->maxLevel = null;
        $this->canTp = null;
        $this->radius = "10%";
        $this->rotate = 0;
    }

    public function createMapWithForm($form)
    {
        if (intval($form["previous_id"]) > 0) {
            $this->parent = $form["previous_id"];//check if this id exist in $_SESSION [map]
        }
        if ($form["name"] != '') {
            $this->name = $form["name"];
        }
        if ($form["image"] != '') {
            $this->url = $form["image"];
        }
        $this->isCity = intval($form["city"]);
        if ($form["min_level"] > 0) {
            $this->minLevel = $form["min_level"];
        }
        if ($form["max_level"] > 10) {
            $this->maxLevel = $form["max_level"];
        }
        $this->canTp = intval($form["can_tp"]);
    }

    public function saveMapBdd()
    {
        unset($this->id);
        unset($this->children);
        if (is_a($this, 'item_set')) {
            unset($this->price);
        }
        $req = "INSERT INTO `" . get_class($this) . "`(";
        $i = 0;
        foreach ($this as $key => $value) {
            if ($i == 0) {
                $req = $req . "`" . $key . "`";
            } else {
                $req = $req . ", `" . $key . "`";
            }
            $i++;
        }
        $req = $req . ") VALUES (";
        $i = 0;
        foreach ($this as $key => $value) {
            if ($i == 0) {
                if (is_int($value) OR is_bool($value)) {
                    $req = $req . intval($value);
                } elseif (is_null($value)) {
                    $req = $req . "null";
                } elseif (is_array($value)) {
                    $req = $req . "'" . addslashes(json_encode($value)) . "'";
                } else {
                    $req = $req . "'" . addslashes($value) . "'";
                }
            } else {
                if (is_int($value) OR is_bool($value)) {
                    $req = $req . ", " . intval($value);
                } elseif (is_null($value)) {
                    $req = $req . ", null";
                } elseif (is_array($value)) {
                    $req = $req . ", '" . addslashes(json_encode($value)) . "'";
                } else {
                    $req = $req . ", '" . addslashes($value) . "'";
                }
            }
            $i++;
        }
        $req = $req . ")";
        $GLOBALS["dbh"]->query($req);
    }

    public function hydrateBDD($data)
    {
        foreach ($data as $key => $value) {
            if (is_int($value)) {
                $this->$key = intval($value);
            } else {
                $newValue = json_decode($value);
                if ($newValue != NULL) {
                    $this->$key = $newValue;
                } else {
                    $this->$key = $value;
                }
            }
        }
        if ($this->time_promotion < time()) {
            $this->promotion = 0;
        }
    }

    private function addMap($map)
    {
        $this->children[$map->id] = $map;
    }

    public function reloadMap()
    {
        if ($this->id != null) {
            return false;
        }
        $this->initialiseNewMap();
        $this->characterSelected = null;
        $this->url = 'http://wow.zamimg.com/images/wow/maps/enus/zoom/-4.jpg?25550';
        $this->name = 'Cosmic Map';
        if (isset($GLOBALS["dbh"])) {
            $req = $GLOBALS["dbh"]->query('SELECT * FROM `map` ORDER BY id DESC');
            while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
                $map = new map();
                $map->hydrateBDD($data);
                $allMaps[$map->id] = $map;
            }
            if (isset($allMaps)) {
                foreach ($allMaps as $key => $map) {
                    if ($map->parent != null) {
                        $allMaps[$map->parent]->addMap($map);
                        unset($allMaps[$map->id]);
                    }
                }
                foreach ($allMaps as $map) {
                    if ($map->parent == null) {
                        $this->addMap($map);
                    }
                }
            }
        }
        return true;
    }

    private function getStyle()
    {
        return "style='top:" . $this->topPos . "%; left:" . $this->leftPos . "%; width:" . $this->width . "%; height:" . $this->height . "%; border-radius: " . $this->radius . "; transform: rotate(" . $this->rotate . "deg);'";
    }

    public function search($oneMap, $searchId)
    {
        $return = null;
        foreach ($oneMap->children as $id => $map) {
            if ($searchId == $id) {
                return $map;
            }
            $result = $this->search($map, $searchId);
            if ($result != null) {
                $return = $result;
            }
        }
        return $return;
    }

    public function getPrice($type)
    {
        $req = $GLOBALS["dbh"]->query("SELECT * FROM `item_home` WHERE `phpclasse`='item_home_teleport'");
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $price = $data["price"];
        }
        $priceVote = $price * VOTE_POINTS;
        $priceBuy = $price * BUY_POINTS;
        $priceVote = intval($priceVote);
        $priceBuy = intval($priceBuy);
        if ($priceVote <= 0) {
            $priceVote = 1;
        }
        if ($priceBuy <= 0) {
            $priceBuy = 1;
        }
        if ($type == 'vote') {
            return $priceVote;
        } else {
            return $priceBuy;
        }
    }

    private function getBuyButtons($votePoints, $buyPoints)
    {
        $return = '
<div class="col-xs-6 noMargin text-center radio"><div style="display: inline-block">
  <label style="float: left"><input type="radio" value="buy" name="optionBuyTeleport">' . $buyPoints . '</label>
  ' . wp_get_attachment_image(168, 'thumbnail', true, ["class" => "img-responsive center-block", "style" => "width:20px;float: left;margin-left: 10px;"]) . '
</div></div>
<div class="col-xs-6 noMargin text-center radio"><div style="display: inline-block">
  <label style="float: left"><input checked="checked" type="radio" value="vote" name="optionBuyTeleport">' . $votePoints . '</label>
  ' . wp_get_attachment_image(169, 'thumbnail', true, ["class" => "img-responsive center-block", "style" => "width:20px;float: left;margin-left: 10px;"]) . '
</div></div>';
        $return .= '<div class="col-xs-12"><button type="submit" class="btn btn-primary btn-block">Teleport me !</button></div>';
        return $return;
    }

    private function getFreeButtons()
    {
        $return = '<div style="margin-top: 25px;" class="col-xs-12 noPadding"><button type="submit" class="btn btn-primary btn-block">Teleport me ! (free)</button></div>';
        return $return;
    }

    private function displayOption($myMap, $allCharacters)
    {
        if (!serverOnline()) {
            return '<button type="button" class="btn btn-danger btn-block disabled">Server is offline</button>';
        }
        if (get_current_user_id() == 0) {
            return '<button type="button" class="btn btn-danger btn-block disabled">You must be connected</button>';
        }
        $return = "";
        $characterLevel = 0;
        $votePoints = $this->getPrice('vote');
        $buyPoints = $this->getPrice('buy');
        if ($this->characterSelected != null) {
            foreach ($allCharacters as $character) {
                if ($character["name"] == $this->characterSelected) {
                    $characterLevel = $character["level"];
                }
            }
            if (($characterLevel < 110 AND $myMap->isCity == 1) OR ($characterLevel >= $myMap->minLevel AND $characterLevel <= $myMap->maxLevel)) {
                $return .= $this->getFreeButtons();
            } else {
                $return .= $this->getBuyButtons($votePoints, $buyPoints);
            }
        } else {
            $return = '<button type="button" class="btn btn-danger btn-block disabled">You must select a character</button>';
        }
        return $return;
    }

    private function displayOneMap($myMap, $type)
    {
        $return = "";
        if ($type == 'map') {
            $return .= '<div style="position: relative" class="col-xs-12 noPadding">';
            $return .= '<div style="display: inline-block;position: absolute">';
            if ($myMap->id != null) {
                $return .= '<i onclick="displayOneMap(null)" class="fa fa-map fa-2x" aria-hidden="true"></i><br/>';
            }
            if ($myMap->parent != null) {
                $return .= '<i onclick="displayOneMap(' . $myMap->parent . ')" class="fa fa-arrow-circle-left fa-2x" aria-hidden="true"></i><br/>';
            }
            if (isWowAdmin() AND $myMap->id != null) {
                $return .= '<p style="background-color: black">' . $myMap->id . '</p>';
            }
            $return .= '</div>';
            $return .= ' <img class="img-responsive" src = "' . $myMap->url . '" alt = "' . $myMap->name . '" ></div > ';
            foreach ($myMap->children as $map) {
                $return .= '<div onclick = "displayOneMap(' . $map->id . ')" class="map" ' . $map->getStyle() . ' ></div > ';
            }
        } elseif ($type == 'option') {
            if ($myMap->canTp == 1) {
                $item_home_teleport = new item_home_teleport();
                $allCharacters = $item_home_teleport->getCharacters();
                $return .= '<form data-map="' . $myMap->id . '" id="teleportThisCharacter" method="post">';
                $return .= '<input name="map_id" type="hidden" value="' . $myMap->id . '">';
                $return .= '<div class="col-xs-6">';
                $return .= $item_home_teleport->displayAllCharacters($allCharacters, $this->characterSelected);
                $return .= '</div>';
                $return .= '<div class="col-xs-6 noPadding">';
                $return .= $this->displayOption($myMap, $allCharacters);
                $return .= '</div>';
                $return .= '</form>';
            }
        }
        return $return;
    }

    public function display($id = null, $type = null)
    {
        $return = "";
        if ($id == null) {
            $return .= $this->displayOneMap($this, $type);
        } else {
            $map = $this->search($this, $id);
            $return .= $this->displayOneMap($map, $type);
        }
        return $return;
    }

    public function getId()
    {
        return $this->id;
    }
}