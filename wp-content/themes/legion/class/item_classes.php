<?php

include_once("parent_item.php");

class item_classes extends parent_item
{
    public $class_id = null;
    public $subclasses = null;
    private $allItemClass = null;

    function __construct($item = null, $allItemClass = null)
    {
        if ($item != null AND $allItemClass != null) {
            $this->class_id = $item->itemClass;
            $this->allItemClass = $allItemClass;
            $this->name = $this->getClassName();
            $this->subclasses = $this->getSubClass($item);
        }
        unset($this->promotion);
        unset($this->time_promotion);
    }

    public function getClassName()
    {
        if ($this->name != null) {
            return $this->name;
        }
        foreach ($this->allItemClass->classes as $value) {
            if ($value->class == $this->class_id) {
                return $value->name;
            }
        }
        return null;
    }

    public function getSubClass()
    {
        if ($this->subclasses != null) {
            foreach ($this->subclasses as $value) {
                if ($value->class == $this->class_id) {
                    return $value->subclasses;
                }
            }
        }
        foreach ($this->allItemClass->classes as $value) {
            if ($value->class == $this->class_id) {
                return $value->subclasses;
            }
        }
        return null;
    }

    public function getSubClassName($item)
    {
        foreach ($this->subclasses as $subClass) {
            if ($subClass->subclass == $item->itemSubClass) {
                return $subClass->name;
            }
        }
        return null;
    }
}