<?php
require_once("../config.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/wp-content/themes/legion/class/item.php");

if (!isset($_POST)) {
    throw new Exception("No data");
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

if ($_POST['id'] == 'previewItem') {
    $item = createItem($_POST['item_id'], $_POST['item_price']);
    echo($item->display());
}

if ($_POST['id'] == 'addItem') {
    $item = createItem($_POST['item_id'], $_POST['item_price']);
    insertItemInBdd($item, $dbh);
}