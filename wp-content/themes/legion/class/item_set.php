<?php

class item_set
{
    public $item_set_id = null;
    public $name = null;
    public $setBonuses = null;
    public $items = null;
    public $price = 0;
    public $item_classes = null;
    public $allowableClasses = null;
    public $vote = 0;

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
    }

    public function hydrateAPI($data)
    {
        if (isset($data->id)) {
            $this->item_set_id = $data->id;
        }
        if (isset($data->name)) {
            $this->name = $data->name;
        }
        if (isset($data->setBonuses)) {
            $this->setBonuses = $data->setBonuses;
        }
        if (isset($data->items)) {
            $this->items = $data->items;
        }
    }

    public function generateInsertRequest()
    {
        $req = "INSERT INTO `item_set`(";
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
                } elseif (is_array($value)) {
                    $req = $req . "'" . addslashes(json_encode($value)) . "'";
                } else {
                    $req = $req . "'" . addslashes($value) . "'";
                }
            } else {
                if (is_int($value) OR is_bool($value)) {
                    $req = $req . ", " . intval($value);
                } elseif (is_array($value)) {
                    $req = $req . ", '" . addslashes(json_encode($value)) . "'";
                } else {
                    $req = $req . ", '" . addslashes($value) . "'";
                }
            }
            $i++;
        }
        $req = $req . ")";
        return $req;
    }

    function display($dbh)
    {
        $return = '<div class="display_item_set display_item" style="padding-left: 20px;padding-right: 20px">';
        $i = 0;
        foreach ($this->items as $itemID) {
            if ($i % 2 == 0) {
                $return = $return . "<div class='row'>";
            }
            $return = $return . previewItem($itemID, '', $dbh, $this->vote, true);
            if ($i % 2 != 0) {
                $return = $return . "</div>";
            }
            $i++;
        }
        $return = $return . '</div>';
        return $return;
    }

    function smallDisplay($dbh)
    {
        $return = '';
        $allImages = $this->getAllItemImageOfTheSet($this->items, $dbh);
        if ($allImages == null) {

        } elseif ($allImages == false) {

        } else {
            foreach ($allImages as $setID) {
                $dataShow["value"] = $this->item_set_id;
                $dataShow["id"] = "previewItemSet";
                $json = json_encode($dataShow);
                $return = $return . "<a class='pinterest' data-show='" . $json . "' onclick='showMoreShop(this)'><li class='list-group-item col-sm-4 col-xs-12'><div class='display_item'>";
                foreach ($setID as $oneImage) {
                    $return = $return . '<img src="https://wow.zamimg.com/images/wow/icons/large/' . $oneImage . '.jpg" alt="' . $oneImage . '">';
                }
                $return = $return . '</div></li></a>';
            }
        }
        return $return;
    }

    function getAllItemImageOfTheSet($allID, $dbh)
    {
        $tab = array();
        $result = array();
        $req = 'SELECT `icon`, `itemSet` FROM `item` WHERE `item_id`=' . $allID[0];
        $i = 0;
        foreach ($allID as $idItem) {
            if ($i > 0) {
                $req = $req . ' OR `item_id`=' . $allID[$i];
            }
            $i++;
        }
        $req = $dbh->query($req);
        if ($req == false) {
            return null;
        } else {
            while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
                array_push($tab, $data["icon"], $data["itemSet"]);
            }
            $i = 0;
            $y = 0;
            $currentSet = $tab[1];
            foreach ($tab as $value) {
                if ($i % 2 != 0) { //item set id
                    $currentSet = $value;
                } else { // icon
                    $result[$currentSet][$y] = $value;
                    $y++;
                }
                $i++;
            }
            return $result;
        }
    }
}