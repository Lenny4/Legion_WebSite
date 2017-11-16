<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/wp-config.php");

if (strpos($_SERVER["HTTP_REFERER"], $_SERVER["SERVER_NAME"]) == false) {//check if request is from my site
    echo("Not allowed");
    return;
}

$GLOBALS["shop_page_id"] = 0;

$pages = get_pages(array(
    'meta_value' => 'shop.php'
));
foreach ($pages as $page) {
    $GLOBALS["shop_page_id"] = $page->ID;
}

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
        if ($item_price != null) {
            $item->price = $item_price;
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

function getItemByClassAndSubClass($subClassId, $classId)
{
    $req = $GLOBALS["dbh"]->query('SELECT * FROM `item` WHERE `itemSubClass`=' . $subClassId . ' AND `itemClass`=' . $classId);
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
    $req = $GLOBALS["dbh"]->query('SELECT * FROM `item_set` WHERE `allowableClasses` LIKE \'%' . $searchClass . '%\';');
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

function viewItems($subClassId, $classId)
{
    $subClassId = intval($subClassId);
    $classId = intval($classId);
    $result = getItemByClassAndSubClass($subClassId, $classId);
    if ($result == null AND sizeof($result) > 0) {
        echo 'Error !';
    } elseif (sizeof($result) == 0) {
        echo 'No Result !';
    } else {
        foreach ($result as $item) {
            $itemClass = createItemClass($item);
            echo($item->display($itemClass, true));
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

//VIEW======================================================

//ADD======================================================
function addItemBdd($postItemId, $postItemPrice, $vote = 0)
{
    $item = createItem($postItemId, $postItemPrice, $vote);
    $itemClass = createItemClass($item);
    insertItemInBdd($item);
    insertItemClassInBdd($itemClass);
    return $item;
}

function addItemSetBdd($postItemSetId, $postItemSetPrice, $vote = 0)
{
    $item_set = createItemSet($postItemSetId, $postItemSetPrice, $vote);
    foreach ($item_set->items as $itemID) {
        addItemBdd($itemID, '', $vote);
    }
    insertItemSetInBdd($item_set);
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

//ADMIN SHOP======================================================

//SHOP======================================================

if ($_POST['id'] == 'subItemClasse') {
    viewItems($_POST['subClassId'], $_POST['classId']);
}

if ($_POST['id'] == 'subItemSetClasse') {
    viewItemSets($_POST['searchClass']);
}

//SHOP======================================================

if ($_POST["id"] == "showHomeItems") {
    $allItemsHome = new item_home();
    echo $allItemsHome->display();
}