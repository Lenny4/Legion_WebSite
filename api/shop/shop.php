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
function createItem($POSTitem_id, $POSTitem_price, $vote = 0)
{
    $item_id = intval($POSTitem_id);
    $item = getItemInBdd($item_id);
    if ($item->item_id == null) {
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
function createItemSet($POSTitem_set_id, $POSTitem_set_price, $vote = 0)
{
    $item_set_id = intval($POSTitem_set_id);
    $item_set = getItemSetInBdd($item_set_id);
    if ($item_set->item_set_id == null) {
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
            $itemSet = new item_set();
            $itemSet->hydrateBDD($data);
            array_push($return, $itemSet);
        }
    }
    return $return;
}

//ITEM SET======================================================

//VIEW======================================================
function previewItem($postItemId, $postItemPrice, $vote = 0, $justReturn = false)
{
    $item = createItem($postItemId, $postItemPrice, $vote);
    $itemClass = createItemClass($item);
    if ($justReturn == false) {
        echo($item->display($itemClass));
    } else {
        return $item->display($itemClass);
    }
}

function previewItemSet($postItemSetId, $postItemSetPrice, $vote = 0)
{
    $item_set = createItemSet($postItemSetId, $postItemSetPrice, $vote);
    echo($item_set->display($GLOBALS["dbh"]));
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

function viewItemSets($searchClass)
{
    $result = getItemSetByClass($searchClass);
    if ($result == null AND sizeof($result) > 0) {
        echo 'Error !';
    } elseif (sizeof($result) == 0) {
        echo 'No Result !';
    } else {
        foreach ($result as $itemSet) {
            echo($itemSet->smallDisplay($GLOBALS["dbh"]));
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
            if ($item->requiredLevel == 110) {
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

//SHOP======================================================
