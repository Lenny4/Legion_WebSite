<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 27/11/2017
 * Time: 18:36
 */

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
            $echo .= "Nothing in your cat";
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
}