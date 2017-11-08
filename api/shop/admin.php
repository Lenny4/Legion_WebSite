<?php
require_once("../config.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/wp-content/themes/legion/class/item.php");

if (!isset($_POST)) {
    throw new Exception("No data");
}

function createItemClass($item)
{
    $allItemClass = json_decode(file_get_contents('https://us.api.battle.net/wow/data/item/classes?locale=en_US&apikey=' . API_KEY));
    $itemClass = new item_classes($item, $allItemClass);
    return $itemClass;
}

function createItem($POSTitem_id, $POSTitem_price)
{
    $item_id = intval($POSTitem_id);
    $item_price = null;
    if ($POSTitem_price != '') {
        $item_price = intval($POSTitem_price);
    }
    $result = json_decode(file_get_contents('https://us.api.battle.net/wow/item/' . $item_id . '?locale=en_US&apikey=' . API_KEY));
    $item = new item();
    $item->hydrateAPI($result);
    if ($item_price != null) {
        $item->price = $item_price;
    }
    return $item;
}

function insertItemInBdd($item, $dbh)
{
    $searchItem = getItemInBdd($item->item_id, $dbh);
    if ($searchItem == null) {
        echo "An error occur";
        return;
    }
    if ($searchItem->item_id == $item->item_id) {
        echo "Already in database -> " . $item->item_id . ':' . $item->name;
        return;
    }
    $req = $item->generateInsertRequest();
    $dbh->query($req);
    $item = getItemInBdd($item->item_id, $dbh);
    if ($item != null) {
        echo "Item added -> " . $item->item_id . ':' . $item->name;
    } else {
        echo "Error while add item";
    }
}

function insertItemClassInBdd($itemClass, $item, $dbh)
{
    $searchItemClass = getItemClassInBdd($itemClass->class_id, $dbh, $item);
    if ($searchItemClass == null) {
        echo "An error occur";
        return;
    }
    if ($searchItemClass->class_id == $itemClass->class_id) {
        //no message here -> item class already in database
        return;
    }
    $req = $itemClass->generateInsertRequest();
    $dbh->query($req);
}

function getItemInBdd($itemID, $dbh)
{
    $item = new item();
    $req = $dbh->query('SELECT * FROM `item` WHERE `item_id`=' . $itemID);
    if ($req == false) {
        return null;
    } else {
        while ($data = $req->fetch()) {
            $item->hydrateBDD($data);
        }
        return $item;
    }
}

function getItemClassInBdd($itemClassID, $dbh, $item)
{
    $itemClass = new item_classes();
    $req = $dbh->query('SELECT * FROM `item_classes` WHERE `class_id`=' . $itemClassID);
    if ($req == false) {
        return null;
    } else {
        while ($data = $req->fetch()) {
            $itemClass->hydrateBDD($data);
        }
        return $itemClass;
    }
}

if ($_POST['id'] == 'previewItem') {
    $item = createItem($_POST['item_id'], $_POST['item_price']);
    $itemClass = createItemClass($item);
    echo($item->display($itemClass));
}

if ($_POST['id'] == 'addItem') {
    $item = createItem($_POST['item_id'], $_POST['item_price']);
    $itemClass = createItemClass($item);
    insertItemInBdd($item, $dbh);
    insertItemClassInBdd($itemClass, $item, $dbh);
}