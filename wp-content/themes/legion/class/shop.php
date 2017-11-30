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
        $item = createItem($id);
        if (!in_array($item, $this->array)) {
            array_push($this->array, $item);
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
}