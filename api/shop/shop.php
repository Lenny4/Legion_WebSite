<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/wp-config.php");

if (strpos($_SERVER["HTTP_REFERER"], $_SERVER["SERVER_NAME"]) == false) {//check if request is from my site
    echo("Not allowed");
    return;
}

//STATIC DATA===========
$req = $GLOBALS["dbh"]->query('SELECT * FROM `static_data_shop`');
while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
    $GLOBALS["max_item_id"] = $data["max_item_id"];
}

$GLOBALS["shop_page_id"] = 0;

$pages = get_pages(array(
    'meta_value' => 'shop.php'
));
foreach ($pages as $page) {
    $GLOBALS["shop_page_id"] = $page->ID;
}
$GLOBALS["max_items_display"] = 50;
//STATIC DATA===========

if (!isset($_POST)) {
    echo("No data");
}

//ITEM======================================================
function createItem($POSTitem_id, $POSTitem_price = '', $vote = 0, $mustBeInBdd = false)
{
    $item_id = intval($POSTitem_id);
    $item = getItemInBdd($item_id);
    if ($item->item_id == null AND $mustBeInBdd == false) {
        $item_price = null;
        if ($POSTitem_price != '') {
            $item_price = intval($POSTitem_price);
        }
        @$result = json_decode(file_get_contents('https://us.api.battle.net/wow/item/' . $item_id . '?locale=en_US&apikey=' . API_KEY));
        if ($result == null) {
            echo '<div class="alert alert-danger"><strong>Not Found</strong></div>';
            die();
        }
        $item = new item();
        $item->hydrateAPI($result);
        if ($item_price == 0 OR $item_price == '') {
            $item->generatePrice();
        }
        $item->vote = intval($vote);
    }
    return $item;
}

function getItemInBdd($itemID)
{
    $item = new item();
    $req = $GLOBALS["dbh"]->query('SELECT * FROM `item` WHERE `item_id`=' . $itemID);
    if ($req == false) {
        return null;
    } else {
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $item->hydrateBDD($data);
        }
        return $item;
    }
}

function insertItemInBdd($item)
{
    $searchItem = getItemInBdd($item->item_id);
    if ($searchItem == null) {
        echo "An error occur<br/>";
        return;
    }
    if ($searchItem->item_id == $item->item_id) {
        echo "Already in database (Item) -> " . $item->item_id . ':' . $item->name . "<br/>";
        return;
    }
    $req = $item->generateInsertRequest();
    $GLOBALS["dbh"]->query($req);
    $item = getItemInBdd($item->item_id);
    if ($item != null) {
        echo "Item added -> " . $item->item_id . ':' . $item->name . "<br/>";
    } else {
        echo "Error while add item<br/>";
    }
}

function getItemByClassAndSubClass($subClassId, $classId, $lastItemId)
{
    $lastItemId = intval($lastItemId);
    if ($lastItemId == 0) {
        $req = $GLOBALS["dbh"]->query('SELECT * FROM `item` WHERE `itemSubClass`=' . intval($subClassId) . ' AND `itemClass`=' . intval($classId) . ' ORDER BY `item_id` DESC LIMIT ' . $GLOBALS["max_items_display"]);
    } else {
        $req = 'SELECT * FROM `item` WHERE `itemSubClass`=' . intval($subClassId) . ' AND `itemClass`=' . intval($classId);
        $req .= ' AND `item_id` < ' . $lastItemId . ' ORDER BY `item_id` DESC LIMIT ' . $GLOBALS["max_items_display"];
        $req = $GLOBALS["dbh"]->query($req);
    }
    $return = array();
    if ($req == false) {
        return null;
    } else {
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $item = new item();
            $item->hydrateBDD($data);
            array_push($return, $item);
        }
        return $return;
    }
}

//ITEM======================================================

//ITEM CLASS======================================================
function createItemClass($item)
{
    $itemClassID = $item->itemClass;
    $itemClass = getItemClassInBdd($itemClassID);
    if ($itemClass->class_id == null) {
        $allItemClass = json_decode(file_get_contents('https://us.api.battle.net/wow/data/item/classes?locale=en_US&apikey=' . API_KEY));
        $itemClass = new item_classes($item, $allItemClass);
    }
    return $itemClass;
}

function getItemClassInBdd($itemClassID)
{
    $itemClass = new item_classes();
    $req = $GLOBALS["dbh"]->query('SELECT * FROM `item_classes` WHERE `class_id`=' . $itemClassID);
    if ($req == false) {
        return null;
    } else {
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $itemClass->hydrateBDD($data);
        }
        return $itemClass;
    }
}

function insertItemClassInBdd($itemClass)
{
    $searchItemClass = getItemClassInBdd($itemClass->class_id);
    if ($searchItemClass == null) {
        echo "An error occur<br/>";
        return;
    }
    if ($searchItemClass->class_id == $itemClass->class_id) {
        //no message here -> item class already in database
        return;
    }
    $req = $itemClass->generateInsertRequest();
    $GLOBALS["dbh"]->query($req);
}

//ITEM CLASS======================================================

//ITEM SET======================================================
function createItemSet($POSTitem_set_id, $POSTitem_set_price = '', $vote = 0, $mustBeInBdd = false)
{
    $item_set_id = intval($POSTitem_set_id);
    $item_set = getItemSetInBdd($item_set_id);
    if ($item_set->item_set_id == null AND $mustBeInBdd == false) {
        $item_set_price = null;
        if ($POSTitem_set_price != '') {
            $item_set_price = intval($POSTitem_set_price);
        }
        @$result = json_decode(file_get_contents('https://us.api.battle.net/wow/item/set/' . $item_set_id . '?locale=en_US&apikey=' . API_KEY));
        if ($result == null) {
            echo '<div class="alert alert-danger"><strong>Not Found</strong></div>';
            die();
        }
        $item_set = new item_set();
        $item_set->hydrateAPI($result);
        if ($item_set_price != null) {
            $item_set->price = $item_set_price;
        }
        $item_set->vote = intval($vote);
        $oneItem = createItem($item_set->items[0], '', $vote);
        $item_set->allowableClasses = json_encode($oneItem->allowableClasses);
    }
    if (intval($item_set->price) == 0) {
        $item_set->price = 0;
        foreach ($item_set->items as $item_id) {
            $oneItem = createItem($item_set->items[0], '', $vote);
            $item_set->price += $oneItem->price;
        }
        $item_set->price = intval($item_set->price);
    }
    return $item_set;
}

function getItemSetInBdd($itemSetID)
{
    $item_set = new item_set();
    $req = $GLOBALS["dbh"]->query('SELECT * FROM `item_set` WHERE `item_set_id`=' . $itemSetID);
    if ($req == false) {
        return null;
    } else {
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $item_set->hydrateBDD($data);
        }
        return $item_set;
    }
}

function insertItemSetInBdd($item_set)
{
    $searchItem = getItemSetInBdd($item_set->item_set_id);
    if ($searchItem == null) {
        echo "An error occur";
        return;
    }
    if ($searchItem->item_set_id == $item_set->item_set_id) {
        echo "Already in database (Item Set) -> " . $item_set->item_set_id . ':' . $item_set->name;
        return;
    }
    $req = $item_set->generateInsertRequest();
    $GLOBALS["dbh"]->query($req);
    $item_set = getItemSetInBdd($item_set->item_set_id);
    if ($item_set != null) {
        echo "Item Set added -> " . $item_set->item_set_id . ':' . $item_set->name;
    } else {
        echo "Error while add item";
    }
}

function getItemSetByClass($searchClass)
{
    $allClasses = array('Warrior', 'Paladin', 'Hunter', 'Rogue', 'Priest', 'Death Knight', 'Shaman', 'Mage', 'Warlock', 'Monk', 'Druid', 'Demon Hunter');
    if (!in_array($searchClass, $allClasses)) {
        return false;
    }
    $req = $GLOBALS["dbh"]->query('SELECT * FROM `item_set` WHERE `allowableClasses` LIKE \'%"' . $searchClass . '"%\' ORDER BY `item_set_id` DESC');
    $return = array();
    if ($req == false) {
        return null;
    } else {
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $itemSet = createItemSet($data["item_set_id"], $data["price"], $data["vote"]);
            array_push($return, $itemSet);
        }
    }
    return $return;
}

//ITEM SET======================================================

//VIEW======================================================
function previewItem($postItemId, $postItemPrice, $vote = 0, $justReturn = false, $dontShowReduction = false)
{
    $item = createItem($postItemId, $postItemPrice, $vote);
    $itemClass = createItemClass($item);
    if ($justReturn == false) {
        echo($item->display($itemClass, false, true, true));
    } else {
        return $item->display($itemClass, false, false, false, $dontShowReduction);
    }
}

function previewItemSet($postItemSetId, $postItemSetPrice, $vote = 0)
{
    $item_set = createItemSet($postItemSetId, $postItemSetPrice, $vote);
    echo($item_set->display(true, true));
}

function viewItems($subClassId, $classId, $lastItemId = 0)
{
    $amountOdItems = 0;
    $subClassId = intval($subClassId);
    $classId = intval($classId);
    $result = getItemByClassAndSubClass($subClassId, $classId, $lastItemId);
    if ($result == null AND sizeof($result) > 0) {
        echo 'Error !';
    } elseif (sizeof($result) == 0) {
        echo 'No Result !';
    } else {
        foreach ($result as $item) {
            $amountOdItems++;
            $itemClass = createItemClass($item);
            echo($item->display($itemClass, true));
            $lastItemId = $item->item_id;
        }
        if ($amountOdItems < $GLOBALS["max_items_display"]) {
            echo '<div id="showMoreItemGlobal" class="col-xs-12">
                    <p class="text-center h4" style="font-family: inherit;">
                        No more ...
                    </p></div>';
        } else {
            echo '<div id="showMoreItemGlobal" class="col-xs-12"><p onclick="showMoreItemGlobal(' . $subClassId . ',' . $classId . ',' . $lastItemId . ')" class="clickable text-center h4 overGreen" style="font-family: inherit;">
            Show more
            <i class="fa fa-arrow-down" aria-hidden="true"></i></p></div>';
        }
    }
}

function viewItemSets($searchClass, $directBuy = false)
{
    $checkThisItemSet = true;
    $result = getItemSetByClass($searchClass);
    if ($result == null AND sizeof($result) > 0) {
        echo 'Error !';
    } elseif (sizeof($result) == 0) {
        echo 'No Result !';
    } else {
        foreach ($result as $itemSet) {
            echo($itemSet->smallDisplay(false, $directBuy, $checkThisItemSet, 110));
            if ($checkThisItemSet == true) {
                $checkThisItemSet = false;
            }
        }
    }
}

function searchItemByIDandName($item_id = null, $item_name = null)
{
    $return = array();
    if ($item_id != null AND $item_id > 0) {
        $req = $GLOBALS["dbh"]->prepare('SELECT * FROM `item` WHERE `item_id` = ?');
        $req->execute(array($item_id));
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $item = new item();
            $item->hydrateBDD($data);
            array_push($return, $item);
        }
    }
    if ($item_name != null AND $item_name != '') {
        $req = $GLOBALS["dbh"]->prepare("SELECT * FROM `item` WHERE `name` LIKE :nameItem LIMIT 50");
        $req->execute(array(':nameItem' => '%' . $item_name . '%'));
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $item = new item();
            $item->hydrateBDD($data);
            array_push($return, $item);
        }
    }
    if ($return == null AND sizeof($return) > 0) {
        echo 'Error !';
    } elseif (sizeof($return) == 0) {
        echo 'No Result !';
    } else {
        foreach ($return as $item) {
            $itemClass = createItemClass($item);
            echo($item->display($itemClass, true));
        }
    }
}

//VIEW======================================================

//ADD======================================================
function addItemBdd($postItemId, $postItemPrice, $vote = 0, $filtre = false)
{
    $item = createItem($postItemId, $postItemPrice, $vote);
    if ($item->item_id > $GLOBALS["max_item_id"]) {
        echo "The id of this item is to high, check the extension";
        return null;
    }
    if ($item->item_id)
        if ($filtre == true AND $item->equippable == true) {
            if ($item->requiredLevel == 110) {
                $itemClass = createItemClass($item);
                insertItemInBdd($item);
                insertItemClassInBdd($itemClass);
            }
        } else {
            $itemClass = createItemClass($item);
            insertItemInBdd($item);
            insertItemClassInBdd($itemClass);
        }
    return $item;
}

function addItemSetBdd($postItemSetId, $postItemSetPrice, $vote = 0, $filtre = false)
{
    $item_set = createItemSet($postItemSetId, $postItemSetPrice, $vote);
    foreach ($item_set->items as $itemID) {
        $item = createItem($itemID, '');
        if ($filtre == true AND $item->equippable == true) {
            if ($item->requiredLevel >= 100) {
                $filtre = false;
            } else {
                return;
            }
        }
        if ($filtre == false) {
            if ($item->item_id > $GLOBALS["max_item_id"]) {
                echo "The id of this item is to high, check the extension";
                return null;
            }
            addItemBdd($itemID, '', $vote);
        }
    }
    if ($filtre == false) {
        insertItemSetInBdd($item_set);
    }
}

function proposeNewItems($allItemsID, $allItemsSetID)
{
    $higherItemId = 0;
    $req = $GLOBALS["dbh"]->query('SELECT * FROM `static_data_shop`');
    while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
        $higherItemId = $data["max_item_id"];
    }
    $itemsAlreadyAsked = array();
    $itemsSetAlreadyAsked = array();
    $itemAlreadyInBdd = array();
    $itemSetAlreadyInBdd = array();
    $itemRefused = array();
    $itemSetRefused = array();
    $itemNotFound = array();
    $itemSetNotFound = array();
    if (is_user_logged_in() == false) {
        echo '<div class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>You must be login !</strong>
</div>';
        return false;
    }
    if (sizeof($allItemsID) == 0 AND sizeof($allItemsSetID) == 0) {
        echo '<div class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>You must write IDs</strong>
</div>';
        return false;
    }
    //Check if asked items are already in bdd
    if (sizeof($allItemsID) > 0) {
        $req = 'SELECT * FROM `item`';
        $i = 0;
        foreach ($allItemsID as $id) {
            if ($i == 0) {
                $req .= ' WHERE ( item_id=' . $id;
            } else {
                $req .= ' OR item_id=' . $id;
            }
            $i++;
        }
        $req .= ')';
        $req = $GLOBALS["dbh"]->query($req);
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            array_push($itemAlreadyInBdd, $data["item_id"]);
        }
    }
    if (sizeof($allItemsSetID) > 0) {
        $req = 'SELECT * FROM `item_set`';
        $i = 0;
        foreach ($allItemsSetID as $id) {
            if ($i == 0) {
                $req .= ' WHERE ( item_set_id=' . $id;
            } else {
                $req .= ' OR item_set_id=' . $id;
            }
            $i++;
        }
        $req .= ')';
        $req = $GLOBALS["dbh"]->query($req);
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            array_push($itemSetAlreadyInBdd, $data["item_set_id"]);
        }
    }
    //Check if item has been refused
    if (sizeof($allItemsID) > 0) {
        $req = 'SELECT * FROM `ask_new_items` WHERE answer=0';
        $i = 0;
        foreach ($allItemsID as $id) {
            if ($i == 0) {
                $req .= ' AND ( item_id=' . $id;
            } else {
                $req .= ' OR item_id=' . $id;
            }
            $i++;
        }
        $req .= ')';
        $req = $GLOBALS["dbh"]->query($req);
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            array_push($itemRefused, $data["item_id"]);
        }
    }
    if (sizeof($allItemsSetID) > 0) {
        $req = 'SELECT * FROM `ask_new_items` WHERE answer=0';
        $i = 0;
        foreach ($allItemsSetID as $id) {
            if ($i == 0) {
                $req .= ' AND ( item_set_id=' . $id;
            } else {
                $req .= ' OR item_set_id=' . $id;
            }
            $i++;
        }
        $req .= ')';
        $req = $GLOBALS["dbh"]->query($req);
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            array_push($itemSetRefused, $data["item_set_id"]);
        }
    }
    //Check if he asked this item before
    if (sizeof($allItemsID) > 0) {
        $req = 'SELECT * FROM `ask_new_items_user` WHERE user_wp_id =' . get_current_user_id();
        $i = 0;
        foreach ($allItemsID as $id) {
            if ($i == 0) {
                $req .= ' AND ( ask_new_items_id=' . $id;
            } else {
                $req .= ' OR ask_new_items_id=' . $id;
            }
            $i++;
        }
        $req .= ')';
        $req = $GLOBALS["dbh"]->query($req);
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            array_push($itemsAlreadyAsked, $data["ask_new_items_id"]);
        }
    }
    if (sizeof($allItemsSetID) > 0) {
        $req = 'SELECT * FROM `ask_new_items_user` WHERE user_wp_id =' . get_current_user_id();
        $i = 0;
        foreach ($allItemsSetID as $id) {
            if ($i == 0) {
                $req .= ' AND ( ask_new_items_set_id=' . $id;
            } else {
                $req .= ' OR ask_new_items_set_id=' . $id;
            }
            $i++;
        }
        $req .= ')';
        $req = $GLOBALS["dbh"]->query($req);
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            array_push($itemsSetAlreadyAsked, $data["ask_new_items_set_id"]);
        }
    }
    //Keep all items who must be found
    foreach ($itemsAlreadyAsked as $value) {
        if (($key = array_search($value, $allItemsID)) !== false) {
            unset($allItemsID[$key]);
        }
    }
    foreach ($itemsSetAlreadyAsked as $value) {
        if (($key = array_search($value, $allItemsSetID)) !== false) {
            unset($allItemsSetID[$key]);
        }
    }
    foreach ($itemAlreadyInBdd as $value) {
        if (($key = array_search($value, $allItemsID)) !== false) {
            unset($allItemsID[$key]);
        }
    }
    foreach ($itemSetAlreadyInBdd as $value) {
        if (($key = array_search($value, $allItemsSetID)) !== false) {
            unset($allItemsSetID[$key]);
        }
    }
    foreach ($itemRefused as $value) {
        if (($key = array_search($value, $allItemsID)) !== false) {
            unset($allItemsID[$key]);
        }
    }
    foreach ($itemSetRefused as $value) {
        if (($key = array_search($value, $allItemsSetID)) !== false) {
            unset($allItemsSetID[$key]);
        }
    }
    //Check if item id is not to high
    foreach ($allItemsID as $value) {
        if ($value > $higherItemId) {
            echo '<div class="alert alert-warning alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  Item <strong>' . $value . '</strong> might have a too high expansion (if not it will be added)
</div>';
        }
    }
    //Check if item Exist
    foreach ($allItemsID as $key => $item_id) {
        @$result = json_decode(file_get_contents('https://us.api.battle.net/wow/item/' . $item_id . '?locale=en_US&apikey=' . API_KEY));
        if ($result == null) {
            array_push($itemNotFound, $allItemsID[$key]);
            unset($allItemsID[$key]);
        }
    }
    foreach ($allItemsSetID as $key => $item_set_id) {
        @$result = json_decode(file_get_contents('https://us.api.battle.net/wow/item/set/' . $item_set_id . '?locale=en_US&apikey=' . API_KEY));
        if ($result == null) {
            array_push($itemSetNotFound, $allItemsSetID[$key]);
            unset($allItemsSetID[$key]);
        } //Check if item id is not to high
        else {
            $thisItemId = $result->items[0];
            @$result = json_decode(file_get_contents('https://us.api.battle.net/wow/item/' . $thisItemId . '?locale=en_US&apikey=' . API_KEY));
            if ($result->id > $higherItemId) {
                echo '<div class="alert alert-warning alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  Item set <strong>' . $item_set_id . '</strong> might have a too high expansion (if not it will be added)
</div>';
            }
        }
    }
    //Add items and items set
    foreach ($allItemsID as $value) {
        $result = $GLOBALS["dbh"]->query('SELECT * FROM `ask_new_items` WHERE item_id=' . $value);
        $count = $result->rowCount();
        if ($count == 0) {
            $GLOBALS["dbh"]->query('INSERT INTO `ask_new_items`(`item_id`, `item_set_id`, `number`, `answer`) VALUES (' . $value . ',null,1,null)');
        } else {
            while ($data = $result->fetch(PDO::FETCH_ASSOC)) {
                $number = $data["number"] + 1;
                $id = $data["id"];
                $GLOBALS["dbh"]->query('UPDATE `ask_new_items` SET `number`=' . $number . ' WHERE id=' . $id);
            }
        }
        $GLOBALS["dbh"]->query('INSERT INTO `ask_new_items_user`(`ask_new_items_id`, `ask_new_items_set_id`, `user_wp_id`) VALUES (' . $value . ',null,' . get_current_user_id() . ')');
    }
    foreach ($allItemsSetID as $value) {
        $result = $GLOBALS["dbh"]->query('SELECT * FROM `ask_new_items` WHERE item_set_id=' . $value);
        $count = $result->rowCount();
        if ($count == 0) {
            $GLOBALS["dbh"]->query('INSERT INTO `ask_new_items`(`item_id`, `item_set_id`, `number`, `answer`) VALUES (null,' . $value . ',1,null)');
        } else {
            while ($data = $result->fetch(PDO::FETCH_ASSOC)) {
                $number = $data["number"] + 1;
                $id = $data["id"];
                $GLOBALS["dbh"]->query('UPDATE `ask_new_items` SET `number`=' . $number . ' WHERE id=' . $id);
            }
        }
        $GLOBALS["dbh"]->query('INSERT INTO `ask_new_items_user`(`ask_new_items_id`, `ask_new_items_set_id`, `user_wp_id`) VALUES (null,' . $value . ',' . get_current_user_id() . ')');
    }
    //Display error/messages
    foreach ($itemsAlreadyAsked as $value) {
        echo '<div class="alert alert-info alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  You have already asked this item : <strong>' . $value . '</strong>
</div>';
    }
    foreach ($itemsSetAlreadyAsked as $value) {
        echo '<div class="alert alert-info alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  You have already asked this item set : <strong>' . $value . '</strong>
</div>';
    }
    foreach ($itemAlreadyInBdd as $value) {
        echo '<div class="alert alert-success alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  Item <strong>' . $value . '</strong> is already in shop ! <a href="#"></a>
</div>';
    }
    foreach ($itemSetAlreadyInBdd as $value) {
        echo '<div class="alert alert-success alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  Item set <strong>' . $value . '</strong> is already in shop ! <a href="#"></a>
</div>';
    }
    foreach ($itemRefused as $value) {
        echo '<div class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  Item <strong>' . $value . '</strong> is not allowed in our server
</div>';
    }
    foreach ($itemSetRefused as $value) {
        echo '<div class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  Item set <strong>' . $value . '</strong> is not allowed in our server
</div>';
    }
    foreach ($itemNotFound as $value) {
        echo '<div class="alert alert-warning alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  Item <strong>' . $value . '</strong> doesn\'t exist
</div>';
    }
    foreach ($itemSetNotFound as $value) {
        echo '<div class="alert alert-warning alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  Item set <strong>' . $value . '</strong> doesn\'t exist
</div>';
    }
    foreach ($allItemsID as $value) {
        echo '<div class="alert alert-success alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  Item <strong>' . $value . '</strong> might be added soon !
</div>';
    }
    foreach ($allItemsSetID as $value) {
        echo '<div class="alert alert-success alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  Item set <strong>' . $value . '</strong> might be added soon !
</div>';
    }
}

//ADD======================================================

//ADMIN SHOP======================================================

if ($_POST['id'] == 'previewItem') {
    previewItem($_POST['item_id'], $_POST['item_price'], $_POST["vote"]);
}

if ($_POST['id'] == 'addItem') {
    if (isWowAdmin()) {
        addItemBdd($_POST['item_id'], $_POST['item_price'], $_POST["vote"]);
    } else {
        echo "Not Allowed !";
    }
}

if ($_POST['id'] == 'previewItemSet') {
    previewItemSet($_POST['item_set_id'], $_POST['item_set_price'], $_POST["vote"]);
}

if ($_POST['id'] == 'addItemSet') {
    if (isWowAdmin()) {
        addItemSetBdd($_POST['item_set_id'], $_POST['item_set_price'], $_POST["vote"]);
    } else {
        echo "Not Allowed !";
    }
}

if ($_POST['id'] == "addAllItem") {
    if (isWowAdmin()) {
        addItemBdd($_POST['currentId'], '', 0, true);
    }
}

if ($_POST['id'] == "addAllItemSet") {
    if (isWowAdmin()) {
        addItemSetBdd($_POST['currentId'], '', 0, true);
    }
}

if ($_POST['id'] == "staticData") {
    if (isWowAdmin()) {
        $max_item_id_allowed = $_POST["max_item_id_allowed"];
        $gold_amount = $_POST["gold_amount"];
        $real_money_amount = $_POST["real_money_amount"];
        $buy_points = $_POST["buy_points"];
        $vote_points = $_POST["vote_points"];
        $GLOBALS["dbh"]->query('INSERT INTO `static_data_shop`(`max_item_id`, `gold_amount`, `real_money_amount`, `buy_points`, `vote_points`) VALUES (' . $max_item_id_allowed . ',' . $gold_amount . ',' . $real_money_amount . ',' . $buy_points . ',' . $vote_points . ')');
        $req = $GLOBALS["dbh"]->query('SELECT * FROM `static_data_shop`');
        $count = $req->rowCount();
        $i = 0;
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            if (++$i != $count) {
                $GLOBALS["dbh"]->query('DELETE FROM `static_data_shop` WHERE `id`=' . $data["id"]);
            }
        }
        echo '
            <div class="alert alert-success alert-dismissable">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Ok</strong>
            </div>
        ';
    }
}

if ($_POST['id'] == "askedItemAdded") {
    $id = $_POST['item_id'];
    $req = $GLOBALS["dbh"]->query('SELECT * FROM `item` WHERE item_id=' . $id);
    if ($req->rowCount() > 0) {
        $allUsers = array();
        $GLOBALS["dbh"]->query('UPDATE `ask_new_items` SET `answer`=1 WHERE item_id=' . $id);
        $req = $GLOBALS["dbh"]->query('SELECT * FROM `ask_new_items_user` WHERE `ask_new_items_id`=' . $id);
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            array_push($allUsers, $data["user_wp_id"]);
        }
        foreach ($allUsers as $userId) {
            $GLOBALS["dbh"]->query("INSERT INTO `message_header`(`message`, `user_id`, `item_id`, `item_set_id`) VALUES ('item_added'," . $userId . "," . $id . ",null)");
        }
        $GLOBALS["dbh"]->query('DELETE FROM `ask_new_items_user` WHERE `ask_new_items_id`=' . $id);
    }
}

if ($_POST['id'] == "askedItemRefused") {
    $id = $_POST['item_id'];
    $allUsers = array();
    $GLOBALS["dbh"]->query('UPDATE `ask_new_items` SET `answer`=0 WHERE item_id=' . $id);
    $req = $GLOBALS["dbh"]->query('SELECT * FROM `ask_new_items_user` WHERE `ask_new_items_id`=' . $id);
    while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
        array_push($allUsers, $data["user_wp_id"]);
    }
    foreach ($allUsers as $userId) {
        $GLOBALS["dbh"]->query("INSERT INTO `message_header`(`message`, `user_id`, `item_id`, `item_set_id`) VALUES ('item_refused'," . $userId . "," . $id . ",null)");
    }
    $GLOBALS["dbh"]->query('DELETE FROM `ask_new_items_user` WHERE `ask_new_items_id`=' . $id);
}

if ($_POST['id'] == "askedItemSetAdded") {
    $id = $_POST['item_set_id'];
    $req = $GLOBALS["dbh"]->query('SELECT * FROM `item_set` WHERE item_set_id=' . $id);
    if ($req->rowCount() > 0) {
        $allUsers = array();
        $GLOBALS["dbh"]->query('UPDATE `ask_new_items` SET `answer`=1 WHERE item_set_id=' . $id);
        $req = $GLOBALS["dbh"]->query('SELECT * FROM `ask_new_items_user` WHERE `ask_new_items_set_id`=' . $id);
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            array_push($allUsers, $data["user_wp_id"]);
        }
        foreach ($allUsers as $userId) {
            $GLOBALS["dbh"]->query("INSERT INTO `message_header`(`message`, `user_id`, `item_id`, `item_set_id`) VALUES ('item_set_added'," . $userId . ",null," . $id . ")");
        }
        $GLOBALS["dbh"]->query('DELETE FROM `ask_new_items_user` WHERE `ask_new_items_set_id`=' . $id);
    }
}

if ($_POST['id'] == "askedItemSetRefused") {
    $id = $_POST['item_set_id'];
    $allUsers = array();
    $GLOBALS["dbh"]->query('UPDATE `ask_new_items` SET `answer`=0 WHERE item_set_id=' . $id);
    $req = $GLOBALS["dbh"]->query('SELECT * FROM `ask_new_items_user` WHERE `ask_new_items_set_id`=' . $id);
    while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
        array_push($allUsers, $data["user_wp_id"]);
    }
    foreach ($allUsers as $userId) {
        $GLOBALS["dbh"]->query("INSERT INTO `message_header`(`message`, `user_id`, `item_id`, `item_set_id`) VALUES ('item_set_refused'," . $userId . ",null," . $id . ")");
    }
    $GLOBALS["dbh"]->query('DELETE FROM `ask_new_items_user` WHERE `ask_new_items_set_id`=' . $id);
}

//ADMIN SHOP======================================================

//SHOP======================================================

if ($_POST['id'] == 'subItemClasse') {
    if (isset($_POST['lastItemId'])) {
        viewItems($_POST['subClassId'], $_POST['classId'], $_POST['lastItemId']);
    } else {
        viewItems($_POST['subClassId'], $_POST['classId']);
    }
}

if ($_POST['id'] == 'subItemSetClasse') {
    viewItemSets($_POST['searchClass']);
}

if ($_POST["id"] == "showHomeItems") {
    $allItemsHome = new item_home();
    $allItemsHome->display();
}

if ($_POST['id'] == 'searchItem') {
    $item_id = intval($_POST["search_item_id"]);
    $item_name = $_POST["search_item_name"];
    searchItemByIDandName($item_id, $item_name);
}

if ($_POST["id"] == "customer_add_items") {
    preg_match_all('!\d+!', $_POST["items_id"], $allItemsID);
    preg_match_all('!\d+!', $_POST["items_set_id"], $allItemsSetID);
    proposeNewItems($allItemsID[0], $allItemsSetID[0]);
}

if ($_POST["id"] == "deleteMessageHeader") {
    $id = $_POST["messageId"];
    $GLOBALS["dbh"]->query('DELETE FROM `message_header` WHERE id=' . $id);
}

if ($_POST["id"] == "showMoreItemHome") {
    $phpClass = $_POST["phpClass"];
    if ($phpClass == 'item_home_teleport') {
        $_SESSION["map"]->reloadMap();
    }
    $item_home = new $phpClass();
    echo $item_home->show();
}

//SHOP======================================================

//SHOP ADMIN======================================================
if ($_POST["id"] == "update_promotion_item_admin") {
    if (!isWowAdmin()) {
        return;
    }
    if (isset($_POST["item_id"])) {
        $item_id = intval($_POST["item_id"]);
        $pourcent = intval($_POST["pourcent"]);
        $date = intval(strtotime($_POST["date"]));
        if ($item_id <= 0 OR $pourcent <= 0 OR $date <= time()) {
            echo '<div class="alert alert-danger alert-dismissable">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Error !</strong>
        </div>';
            return;
        }
        $GLOBALS["dbh"]->query('UPDATE `item` SET `promotion`=' . $pourcent . ', `time_promotion`=' . $date . ' WHERE `item_id`=' . $item_id);
        echo '<div class="alert alert-success alert-dismissable">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Promotion update</strong>
        </div>';
    } elseif (isset($_POST["item_set_id"])) {
        $item_id = intval($_POST["item_set_id"]);
        $pourcent = intval($_POST["pourcent"]);
        $date = intval(strtotime($_POST["date"]));
        if ($item_id <= 0 OR $pourcent <= 0 OR $date <= time()) {
            echo '<div class="alert alert-danger alert-dismissable">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Error !</strong>
        </div>';
            return;
        }
        $GLOBALS["dbh"]->query('UPDATE `item_set` SET `promotion`=' . $pourcent . ', `time_promotion`=' . $date . ' WHERE `item_set_id`=' . $item_id);
        echo '<div class="alert alert-success alert-dismissable">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Promotion update</strong>
        </div>';
    }
}

if ($_POST["id"] == "removePromotion") {
    if (!isWowAdmin()) {
        return;
    }
    if (isset($_POST["item_id"])) {
        $item_id = intval($_POST["item_id"]);
        if ($item_id <= 0) {
            echo '<div class="alert alert-success alert-dismissable">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Error !</strong>
        </div>';
        } else {
            $GLOBALS["dbh"]->query('UPDATE `item` SET `promotion`=0, `time_promotion`=0 WHERE `item_id`=' . $item_id);
            echo '<div class="alert alert-success alert-dismissable">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Promotion delete</strong>
        </div>';
        }
    } elseif (isset($_POST["item_set_id"])) {
        $item_id = intval($_POST["item_set_id"]);
        if ($item_id <= 0) {
            echo '<div class="alert alert-success alert-dismissable">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Error !</strong>
        </div>';
        } else {
            $GLOBALS["dbh"]->query('UPDATE `item_set` SET `promotion`=0, `time_promotion`=0 WHERE `item_set_id`=' . $item_id);
            echo '<div class="alert alert-success alert-dismissable">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Promotion delete</strong>
        </div>';
        }
    }
}

if ($_POST["id"] == "update_item_admin") {
    if (!isWowAdmin()) {
        return;
    }
    $newPrice = intval($_POST["price"]);
    $item_id = intval($_POST["item_id"]);
    if ($newPrice < 0) {
        echo '<div class="alert alert-success alert-dismissable">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Error !</strong>
        </div>';
    } else {
        $GLOBALS["dbh"]->query('UPDATE `item` SET `price`=' . $newPrice . ' WHERE `item_id`=' . $item_id);
        echo '<div class="alert alert-success alert-dismissable">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Item updated !</strong>
        </div>';
    }

}

if ($_POST["id"] == "removeItem") {
    if (!isWowAdmin()) {
        return;
    }
    if (isset($_POST["item_id"])) {
        $item_id = intval($_POST["item_id"]);
        if ($item_id <= 0) {
            echo '<div class="alert alert-success alert-dismissable">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Error !</strong>
        </div>';
        } else {
            $GLOBALS["dbh"]->query('DELETE FROM `item` WHERE `item_id`=' . $item_id);
            echo '<div class="alert alert-success alert-dismissable">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Item delete</strong>
        </div>';
        }
    } elseif (isset($_POST["item_set_id"])) {
        $item_id = intval($_POST["item_set_id"]);
        if ($item_id <= 0) {
            echo '<div class="alert alert-success alert-dismissable">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Error !</strong>
        </div>';
        } else {
            $GLOBALS["dbh"]->query('DELETE FROM `item_set` WHERE `item_set_id`=' . $item_id);
            echo '<div class="alert alert-success alert-dismissable">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Item set delete (but the items still exists)</strong>
        </div>';
        }
    }
}
//SHOP ADMIN======================================================

//SHOP CART======================================================
if ($_POST["id"] == "addToCart") {
    $id = intval($_POST["item_item_set_id"]);
    $type = $_POST["type"];
    $result = "false";
    if ($id > 0) {
        if ($type == "item") {
            $result = $_SESSION["shop"]->addItem($id);
        } elseif ($type == "item_set") {
            $result = $_SESSION["shop"]->addItemSet($id);
        }
    }
    if ($result == "false") {
        $result = "<div class=\"alert alert-success alert-dismissable\">
  <a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>
  <strong>Already in your cart !</strong>
</div>";
    }
    echo $result;
}

if ($_POST["id"] == "removeToCart") {
    $id = intval($_POST["item_item_set_id"]);
    $type = $_POST["type"];
    if ($id > 0) {
        $_SESSION["shop"]->erase($id, $type);
    }
}

if ($_POST["id"] == "viewItemCart") {
    $id = intval($_POST["item_item_set_id"]);
    $type = $_POST["type"];
    echo $_SESSION["shop"]->view($id, $type);
}

if ($_POST["id"] == "changeSelectedCharacter") {
    if (!isset($_POST["type"])) {
        $_SESSION["shop"]->changeCharacterForAll($_POST["character_name"]);
    } else {
        if ($_POST["type"] == "item") {
            $_SESSION["shop"]->changeCharacterItem($_POST["item_id"], $_POST["character_name"]);
        } elseif ($_POST["type"] == "item_set") {
            $_SESSION["shop"]->changeCharacterItemSet($_POST["item_id"], $_POST["character_name"]);
        }
    }
}

if ($_POST["id"] == "changeQuantity") {
    $result = 1;
    $quantity = intval($_POST["quantity"]);
    $id = intval($_POST["item_id"]);
    if ($_POST["type"] == "item" OR $_POST["type"] == "item_set") {
        $result = $_SESSION["shop"]->changeQuantity($quantity, $id, $_POST["type"]);
    }
    echo $result;
}

if ($_POST["id"] == "changeCurrency") {
    $currency = $_POST["currency"];
    $type = $_POST["type"];
    $id = intval($_POST["item_id"]);
    if (($type == "item" OR $type == "item_set") AND ($currency == "vote" OR $currency == "buy")) {
        echo $_SESSION["shop"]->changeCurrency($id, $type, $currency);
    }
}
//SHOP CART======================================================

//SHOP BUY CART======================================================
if ($_POST["id"] == "loadBuy") {
    echo $_SESSION["shop"]->loadBuy();
}

if ($_POST["id"] == "buyAllCart") {
    $message = $_SESSION["shop"]->buyAllCart();
    if ($message == "") {
        echo("<div class=\"alert alert-danger\"><strong>An error occur, please reload the page and try again</strong></div>");
    } else {
        echo($message);
    }
}
//SHOP BUY CART======================================================

//SHOP ITEM HOME======================================================
if ($_POST["id"] == "changeCharacterItemHome") {
    $phpClass = $_POST["phpClass"];
    if ($phpClass == 'item_home_teleport') {
        $_SESSION["map"]->characterSelected = $_POST["value"];
    }
    if ($phpClass == 'item_home_level') {
        echo '
<div class="form-group">
     <div class="form-group">
      <label for="level_item_home_level">Level:</label>
      <select name="level" onchange="changeLevelItemHomeLevel(this)" class="form-control" id="level_item_home_level">';
        for ($i = 1; $i <= 110; $i++) {
            echo '<option value="' . $i . '">' . $i . '</option>';
        }
        echo '</select>
    </div> 
  </div>
  <div id="action_item_home_level"></div>
';
    }
    if ($phpClass == 'item_home_character') {
        $character = $_POST["value"];
        $all_characters = new item_home();
        $all_characters = $all_characters->getCharacters();
        echo '<label>Select a item set:</label>';
        foreach ($all_characters as $characters) {
            if ($characters["name"] == $character) {
                $character = $characters;
            }
        }
        viewItemSets($character["class"], true);
        $item_home_character = new item_home_character();
        $vote_points = $item_home_character->getVotePoint();
        $buy_points = $item_home_character->getBuyPoint();
        echo '<div class="col-xs-6 noMargin text-center radio"><div style="display: inline-block">
  <label style="float: left"><input type="radio" value="buy" name="optionBuyCharacter">' . $buy_points . '</label>
  ' . wp_get_attachment_image(168, 'thumbnail', true, ["class" => "img-responsive center-block", "style" => "width:20px;float: left;margin-left: 10px;"]) . '
</div></div>
<div class="col-xs-6 noMargin text-center radio"><div style="display: inline-block">
  <label style="float: left"><input checked="checked" type="radio" value="vote" name="optionBuyCharacter">' . $vote_points . '</label>
  ' . wp_get_attachment_image(169, 'thumbnail', true, ["class" => "img-responsive center-block", "style" => "width:20px;float: left;margin-left: 10px;"]) . '
</div></div>';
    }
}
//SHOP ITEM HOME======================================================

//SHOP TELEPORT======================================================
if ($_POST["id"] == "changePriceTeleport") {
    $GLOBALS["dbh"]->query("UPDATE `item_home` SET `price`=" . $_POST['price'] . " WHERE `phpclasse`='item_home_teleport'");
}

if ($_POST["id"] == "addMapTeleportation") {
    $newMap = new map();
    $newMap->createMapWithForm($_POST);//no link beetween map are created
    $newMap->saveMapBdd();
    $_SESSION["map"]->reloadMap();
    echo $_SESSION["map"]->display();
}

if ($_POST["id"] == "showMap") {
    echo $_SESSION["map"]->display($_POST["map_id"], $_POST["type"]);
}

if ($_POST["id"] == "teleportThisCharacter") {
    if (isset($_POST["optionBuyTeleport"])) {
        $soapTeleporation = new SOAPTeleportation($_POST["map_id"], $_POST["character_selected"], $_POST["optionBuyTeleport"]);
    } else {
        $soapTeleporation = new SOAPTeleportation($_POST["map_id"], $_POST["character_selected"]);
    }
    if ($soapTeleporation->message == '') {
        echo '<div style="display: inline-block;width: 100%;" class="alert alert-success alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>' . $_POST["character_selected"] . ' has been teleport</strong>
</div>';
    } else {
        echo $soapTeleporation->message;
    }
}
//SHOP TELEPORT======================================================

//SHOP LEVEL======================================================
if ($_POST["id"] == "getPriceCharacterLevel") {
    $character = $_POST["name"];
    $level = 0;
    $wantLevel = intval($_POST["value"]);
    $item_home = new item_home();
    $characters = $item_home->getCharacters();
    foreach ($characters as $my_character) {
        if ($my_character["name"] == $character) {
            $level = $my_character["level"];
        }
    }
    if ($level == 0) {
        echo '<div class="alert alert-warning">
  <strong>Error ! Please reload the page</strong>
</div>';
        return;
    }
    if ($wantLevel < 1 OR $wantLevel > 110) {
        echo '<div class="alert alert-warning">
  <strong>You must select a level between 1 and 110</strong>
</div>';
        return;
    }
    if ($wantLevel <= $level) {
        echo '<div class="alert alert-warning">
  <strong>You must select a level higher than the level of you character</strong>
</div>';
        return;
    }
    $prices = getPriceCharacterLevelUp($level, $wantLevel);
    ?>
    <div class="radio col-sm-2 col-sm-offset-2 col-xs-6 text-center" style="margin-top: 0px">
        <?= wp_get_attachment_image(168, 'thumbnail', true, ["class" => "img-responsive"]); ?>
        <label>
            <input checked="checked" value="buy" name="optradio_item_home_level" type="radio">
            <span><?= $prices["buy"]; ?></span>
        </label>
    </div>
    <div class="radio col-sm-2 col-sm-offset-4 col-xs-6 text-center" style="margin-top: 0px">
        <?= wp_get_attachment_image(169, 'thumbnail', true, ["class" => "img-responsive"]); ?>
        <label>
            <input value="buy" name="optradio_item_home_level" type="radio">
            <span><?= $prices["vote"]; ?></span>
        </label>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Level up !</button>
    <?php
}

if ($_POST["id"] == "changeLevelCharacterForm") {
    $character = $_POST["character_selected"];
    $level = 0;
    $wantLevel = intval($_POST["level"]);
    $currency = $_POST["optradio_item_home_level"];
    $item_home = new item_home();
    $characters = $item_home->getCharacters();
    foreach ($characters as $my_character) {
        if ($my_character["name"] == $character) {
            $level = $my_character["level"];
        }
    }
    if (($level == 0) OR ($wantLevel < 1 OR $wantLevel > 110) OR ($wantLevel <= $level)) {
        echo '<div class="col-sm-9 col-xs-12"><div class="alert alert-warning">
  <strong>Error ! Please reload the page</strong>
</div></div>';
        return;
    }
    $prices = getPriceCharacterLevelUp($level, $wantLevel);
    $soapLevelUp = new SOAPLevelUp($character, $wantLevel, $prices, $currency);
    if ($soapLevelUp->message == '') {
        echo '<div class="col-sm-9 col-xs-12"><div style="display: inline-block;width: 100%;" class="alert alert-success alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>' . $_POST["character_selected"] . ' has been level up !</strong>
</div></div>';
    } else {
        echo '<div class="col-sm-9 col-xs-12">' . $soapLevelUp->message . '</div>';
    }
}
//SHOP LEVEL======================================================

//SHOP CHARACTER 110======================================================
if ($_POST["id"] == "buy_character") {
    $item_home_character = new item_home_character();
    $soapBuyCharacter = new SOAPCharacter($_POST["character_selected"], $_POST["item_set_for_item_home_character"], $_POST["optionBuyCharacter"], $item_home_character);
    if ($soapBuyCharacter->message == '') {
        echo '<div class="col-xs-12"><div style="display: inline-block;width: 100%;" class="alert alert-success alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>' . $_POST["character_selected"] . ' has been level up and get the item set !</strong>
</div></div>';
    } else {
        echo '<div class="col-xs-12">' . $soapBuyCharacter->message . '</div>';
    }
}
//SHOP CHARACTER 110======================================================

//SHOP GOLD======================================================
if ($_POST["id"] == "buy_gold") {
    $selectedCharacter = $_POST["character_selected"];
    $currency = $_POST["radio_item_home_gold"];
    $amountOfGold = $_POST["amountOfGold"];
    $parent_item = new parent_item();
    $characters = $parent_item->getCharacters();
    foreach ($characters as $character) {
        if ($character["name"] == $selectedCharacter) {
            $selectedCharacter = $character;
            break;
        }
    }
    if (!is_array($selectedCharacter)) {
        echo '<div style="display: inline-block;width: 100%;" class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error with the character</strong>
</div>';
        return;
    }
    if ($amountOfGold < MIN_AMOUNT_OF_GOLD_BUY OR $amountOfGold > MAX_AMOUNT_OF_GOLD_BUY) {
        echo '<div style="display: inline-block;width: 100%;" class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error amount of gold</strong>
</div>';
        return;
    }
    $maxPourcentReducGold = 0;
    $req = $GLOBALS["dbh"]->query("SELECT * FROM `item_home` WHERE `phpclasse`='item_home_gold'");
    while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
        $maxPourcentReducGold = $data["promotion"];
    }
    $ratioGold = RATIO_GOLD;
    $realMoney = REAL_MONEY;
    $ratioVotePoint = VOTE_POINTS;
    $ratioBuyPoint = BUY_POINTS;
    $maxReduction = $maxPourcentReducGold;
    $minGoldAmount = MIN_AMOUNT_OF_GOLD_BUY;
    $maxGoldAmount = MAX_AMOUNT_OF_GOLD_BUY;
    $domaine = $maxGoldAmount - $minGoldAmount;
    $pourcentage = ($amountOfGold - $minGoldAmount) / $domaine;
    $pourcentage = ($maxReduction * $pourcentage) / 100;
    $realValue = $amountOfGold * ($realMoney / $ratioGold) / 100;
    $realValueReduction = $realValue * (1 - $pourcentage);
    $buyPoint = intval($realValue * $ratioBuyPoint);
    $votePoint = intval($realValue * $ratioVotePoint);
    $buyPointReduction = intval($realValueReduction * $ratioBuyPoint);
    $votePointReduction = intval($realValueReduction * $ratioVotePoint);
    $amount = 0;
    if ($currency == "buy") {
        $currentBuyPoint = intval(get_user_meta(get_current_user_id(), "buy_points")[0]);
        $amount = $buyPointReduction;
        if ($currentBuyPoint < $buyPointReduction) {
            echo '<div style="display: inline-block;width: 100%;" class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>You don\'t have enough buy point</strong>
</div>';
            return;
        }
    } elseif ($currency == "vote") {
        $currentVotePoint = intval(get_user_meta(get_current_user_id(), "vote_points")[0]);
        $amount = $votePointReduction;
        if ($currentVotePoint < $votePointReduction) {
            echo '<div style="display: inline-block;width: 100%;" class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>You don\'t have enough vote point</strong>
</div>';
            return;
        }
    } else {
        echo '<div style="display: inline-block;width: 100%;" class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error ! Please reload the page</strong>
</div>';
        return;
    }
    $SOAPSendMoney = new SOAPSendMoney($selectedCharacter["name"], $currency, $amount, $amountOfGold);
    if ($SOAPSendMoney->message == '') {
        echo '<div style="display: inline-block;width: 100%;" class="alert alert-success alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Your money has been send to ' . $selectedCharacter["name"] . ' !</strong>
</div>';
    } else {
        echo '<div class="col-xs-12">' . $SOAPSendMoney->message . '</div>';
    }
}
//SHOP GOLD======================================================