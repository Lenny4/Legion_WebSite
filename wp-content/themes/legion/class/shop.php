<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 27/11/2017
 * Time: 18:36
 */

include_once("SOAPSendItem.php");

class shop
{
    public $array = array();

    public function erase($id = null, $type = null)
    {
        if ($id == null) {
            $this->array = array();
        }
        foreach ($this->array as $key => $item) {
            if ($type == "item") {
                if (is_a($item, $type) AND $item->item_id == $id) {
                    unset($this->array["$key"]);
                }
            } elseif ($type = "item_set") {
                if (is_a($item, $type) AND $item->item_set_id == $id) {
                    unset($this->array["$key"]);
                }
            }
        }
    }

    public function view($id, $type)
    {
        foreach ($this->array as $key => $item) {
            if ($type == "item") {
                if (is_a($item, $type) AND $item->item_id == $id) {
                    return $this->array["$key"]->displayCart();
                }
            } elseif ($type = "item_set") {
                if (is_a($item, $type) AND $item->item_set_id == $id) {
                    return $this->array["$key"]->displayCart();
                }
            }
        }
    }

    public function addItem($id)
    {
        $result = "false";
        $canAddItem = true;
        $item = createItem($id);
        foreach ($this->array as $itemInArray) {
            if ($itemInArray->item_id == $item->item_id) {
                $canAddItem = false;
                break;
            }
        }
        if ($canAddItem == true) {
            $item->count = 1;
            $item->currency = "vote";
            array_push($this->array, $item);
            $result = "true";
        }
        return $result;
    }

    public function addItemSet($id)
    {
        $result = "false";
        $canAddItem = true;
        $item_set = createItemSet($id);
        foreach ($this->array as $itemInArray) {
            if ($itemInArray->item_set_id == $item_set->item_set_id) {
                $canAddItem = false;
                break;
            }
        }
        if ($canAddItem == true) {
            $item_set->count = 1;
            $item_set->currency = "vote";
            array_push($this->array, $item_set);
            $result = "true";
        }
        return $result;
    }

    public function isEmpty()
    {
        if ($this->array == array()) {
            return true;
        }
        return false;
    }

    public function displayBuy()
    {
        $i = 0;
        $echo = "<div id=\"shopDisplayItems\">";
        if ($this->isEmpty()) {
            $echo .= "Nothing in your cart";
        } else {
            $echo .= '<div class="form-group"><label for="main_character">Select the character to receive your items</label><select class="form-control" id="main_character">
            <option disabled selected value> -- Select a Character -- </option>';
            foreach ($this->array[0]->getCharacters() as $character) {
                $echo .= '
                    <option value="' . $character["name"] . '">' . $character["name"] . ' lvl ' . $character["level"] . '</option>
                  ';
            }
            $echo .= '</select></div>';
        }
        foreach ($this->array as $item) {
            if ($i == 0) {
                $echo .= "<div class='row'>";
            }
            if (is_a($item, 'item')) {
                $echo .= $item->display($item->itemClass, true, false, false, false, true);
            } elseif (is_a($item, 'item_set')) {
                $echo .= $item->smallDisplay(true);
            }
            $i++;
            if ($i == 3) {
                $echo .= "</div>";
                $i = 0;
            }
        }
        $echo .= "</div>";
        echo $echo;
    }

    public function changeCharacterForAll($name)
    {
        foreach ($this->array as $item) {
            $item->character = $name;
        }
    }

    public function changeCharacterItem($id, $name)
    {
        foreach ($this->array as $item) {
            if ($item->item_id == $id AND is_a($item, 'item')) {
                $item->character = $name;
                break;
            }
        }
    }

    public function changeCharacterItemSet($id, $name)
    {
        foreach ($this->array as $item_set) {
            if ($item_set->item_set_id == $id AND is_a($item_set, 'item_set')) {
                $item_set->character = $name;
                break;
            }
        }
    }

    public function changeQuantity($quantity, $id, $type)
    {
        if ($quantity > 100) {
            return 100;
        }
        if ($quantity <= 0) {
            return 1;
        }
        $return = 1;
        foreach ($this->array as $item) {
            if ($type == "item") {
                if ($item->item_id == $id AND is_a($item, 'item')) {
                    $return = $item->count = $quantity;
                    break;
                }
            } elseif ($type == "item_set") {
                if ($item->item_set_id == $id AND is_a($item, 'item_set')) {
                    $return = $item->count = $quantity;
                    break;
                }
            }
        }
        return $return;
    }

    public function changeCurrency($id, $type, $currency)
    {

        foreach ($this->array as $item) {
            if ($type == "item") {
                if ($item->item_id == $id AND is_a($item, 'item')) {
                    $item->currency = $currency;
                    break;
                }
            } elseif ($type == "item_set") {
                if ($item->item_set_id == $id AND is_a($item, 'item_set')) {
                    $item->currency = $currency;
                    break;
                }
            }
        }
    }

    public function loadBuy()
    {
        if (get_current_user_id() == 0) {
            return '<div class="alert alert-danger">
                  <strong>You need to be login to buy item</strong>
                </div>';
        }
        $amountOfVotePoints = 0;
        $amountOfBuyPoints = 0;
        $userVotePoint = intval(get_user_meta(get_current_user_id(), "vote_points")[0]);
        $userBuyPoint = intval(get_user_meta(get_current_user_id(), "buy_points")[0]);
        foreach ($this->array as $item) {
            if ($item->currency == "vote") {
                $amountOfVotePoints += $item->getVotePoint(false, true) * $item->count;
            } elseif ($item->currency == "buy") {
                $amountOfBuyPoints += $item->getBuyPoint(false, true) * $item->count;
            }
        }
        $userVotePointAfterBuy = $userVotePoint - $amountOfVotePoints;
        $userBuyPointAfterBuy = $userBuyPoint - $amountOfBuyPoints;
        $return = "<div class='col-xs-12'>";
        $return .= "<div class='col-xs-6'>
        <div class='col-xs-6 col-xs-offset-3'>" . wp_get_attachment_image(169, 'thumbnail', true, ["class" => "img-responsive"]) . "</div>
        <div class='col-xs-12'><p class='text-center'>" . formatNumber($userVotePoint) . " - " . formatNumber($amountOfVotePoints) . "</p></div>";
        if ($userVotePointAfterBuy >= 0) {
            $return .= "<div class='col-xs-12'><p class='text-center'>" . formatNumber($userVotePointAfterBuy) . "</p></div>";
        } else {
            $return .= "<div class='col-xs-12'><p class='text-center' style='color:red'>" . formatNumber($userVotePointAfterBuy) . "</p></div>";
        }
        $return .= "</div>";
        $return .= "<div class='col-xs-6'>
        <div class='col-xs-6 col-xs-offset-3'>" . wp_get_attachment_image(168, 'thumbnail', true, ["class" => "img-responsive"]) . "</div>
        <div class='col-xs-12'><p class='text-center'>" . formatNumber($userBuyPoint) . " - " . formatNumber($amountOfBuyPoints) . "</p></div>";
        if ($userBuyPointAfterBuy >= 0) {
            $return .= "<div class='col-xs-12'><p class='text-center'>" . formatNumber($userBuyPointAfterBuy) . "</p></div>";
        } else {
            $return .= "<div class='col-xs-12'><p class='text-center' style='color:red'>" . formatNumber($userBuyPointAfterBuy) . "</p></div>";
        }
        $return .= "</div > ";
        if (!serverOnline()) {
            $return .= '<button type="button" class="btn btn-primary btn-block disabled">The server is not online</button>';
        } elseif (empty($this->array[0]->getCharacters())) {
            $return .= '<button type="button" class="btn btn-primary btn-block disabled">You need to create a character</button>';
        } elseif ($userBuyPointAfterBuy >= 0 AND $userVotePointAfterBuy >= 0) {
            $return .= '<button onclick="buyAllCart()" type="button" class="btn btn-primary btn-block">Buy</button>';
        } else {
            $return .= '<button type="button" class="btn btn-primary btn-block disabled">You don\'t have enought points</button>';
        }
        $return .= " </div > ";
        return $return;
    }

    public function buyAllCart()
    {
        //can buy ?
        $loadBuy = $this->loadBuy();
        $point_vote = null;
        $point_buy = null;
        $message = "";
        if (strpos($loadBuy, 'buyAllCart()') !== false) {
            foreach ($this->array as $item) {
                $quantity = $item->stackable * $item->count;
                if ($item->currency == "vote") {
                    $point_vote = $item->getVotePoint(false, true) * $item->count;
                    $point_buy = null;
                } elseif ($item->currency == "buy") {
                    $point_buy = $item->getBuyPoint(false, true) * $item->count;
                    $point_vote = null;
                }
                if (is_a($item, 'item')) {
                    $soapCommand = new SOAPSendItem($item->item_id, $quantity, $point_vote, $point_buy, $item->character, 'item');
                    $message .= $soapCommand->message;
                } elseif (is_a($item, 'item_set')) {
                    $soapCommand = new SOAPSendItem($item->item_id, $quantity, $point_vote, $point_buy, $item->character, 'item_set');
                    $message .= $soapCommand->message;
                }
            }
        }
        return $message;
    }
}