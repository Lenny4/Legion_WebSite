<?php

include_once("parent_item.php");

class item_set extends parent_item
{
    public $item_set_id = null;
    public $setBonuses = null;
    public $items = null;
    public $price = 0;
    public $item_classes = null;
    public $allowableClasses = null;
    public $vote = 0;

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

    function display()
    {
        $return = '<div class="display_item_set display_item" style="padding-left: 20px;padding-right: 20px">';
        $i = 0;
        foreach ($this->items as $itemID) {
            if ($i % 2 == 0) {
                $return = $return . "<div class='row'>";
            }
            $return = $return . previewItem($itemID, '', $this->vote, true);
            if ($i % 2 != 0) {
                $return = $return . "</div>";
            }
            $i++;
        }
        $return = $return . '</div>';
        return $return;
    }

    function smallDisplay()
    {
        $return = '';
        $allInfos = $this->getAllItemInfoOfTheSet($this->items);
        if ($allInfos == null) {

        } elseif ($allInfos == false) {

        } else {
            $dataShow["value"] = $this->item_set_id;
            $dataShow["id"] = "previewItemSet";
            $json = json_encode($dataShow);
            $globalItemLevel = intval(array_sum($allInfos["itemLevel"]) / sizeof($allInfos["itemLevel"]));
            $return = $return . "<a class='pinterest' data-show='" . $json . "' onclick='showMoreShop(this)'><li class='list-group-item col-sm-4 col-xs-12'><div class='display_item display_item_small'>";
            foreach ($allInfos["icon"] as $oneImage) {
                $return = $return . '<img src="https://wow.zamimg.com/images/wow/icons/large/' . $oneImage . '.jpg" alt="' . $oneImage . '">';
            }
            $return = $return . '<p class="name"><span class="name">Name </span><span class="value">"' . $this->name . '"</span></p>';
            $return = $return . '<p class="itemLevel"><span class="itemLevel">Average Item Level </span><span class="value">' . $globalItemLevel . '</span></p>';
            $return = $return . '</div></li></a>';
        }
        return $return;
    }

    function getAllItemInfoOfTheSet($allID)
    {
        $result = array();
        $req = 'SELECT `icon`, `itemLevel` FROM `item` WHERE `item_id`=' . $allID[0];
        $i = 0;
        foreach ($allID as $idItem) {
            if ($i > 0) {
                $req = $req . ' OR `item_id`=' . $allID[$i];
            }
            $i++;
        }
        $req = $GLOBALS["dbh"]->query($req);
        if ($req == false) {
            return null;
        } else {
            $i = 0;
            while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
                $i++;
                $result['itemLevel'][$i] = $data["itemLevel"];
                $result['icon'][$i] = $data["icon"];
                $i++;
            }
            return $result;
        }
    }
}