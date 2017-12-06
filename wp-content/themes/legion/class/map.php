<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 05/12/2017
 * Time: 18:16
 */

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

    function __construct()
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
        $this->children = array();
        $allMaps = array();
        $req = $GLOBALS["dbh"]->query('SELECT * FROM `map` ORDER BY id DESC');
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $map = new map();
            $map->hydrateBDD($data);
            $allMaps[$map->id] = $map;
        }
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

    private function getStyle()
    {
        return "style='left:" . $this->leftPos . "%; top:" . $this->topPos . "%; width:" . $this->width . "%; height:" . $this->height . "%; border-radius: " . $this->radius . ";'";
    }

    private function search($oneMap, $searchId)
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

    private function displayOneMap($myMap)
    {
        $return = "";
        $return .= '<div class="col-xs-12 noPadding"><img class="img-responsive" src="' . $myMap->url . '" alt="' . $myMap->name . '"></div>';
        foreach ($myMap->children as $map) {
            $return .= '<div onclick="displayOneMap(' . $map->id . ')" class="map" ' . $map->getStyle() . ' ></div>';
        }
        return $return;
    }

    public function display($id = null)
    {
        $return = "";
        if ($id == null) {
            $return .= $this->displayOneMap($this);
        } else {
            $map = $this->search($this, $id);
            $return .= $this->displayOneMap($map);
        }
        return $return;
    }
}