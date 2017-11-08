<?php

class item_classes
{
    public $class_id = null;
    public $name = null;
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
    }

    public function generateInsertRequest()
    {
        unset($this->allItemClass);
        $req = "INSERT INTO `item_classes`(";
        $i = 0;
        foreach ($this as $key => $value) {
            if ($i == 0) {
                $req = $req . "`" . $key . "`";
            } else {
                $req = $req . ", `" . $key . "`";
            }
            $i++;
        }
        $req = $req . ") VALUES (";
        $i = 0;
        foreach ($this as $key => $value) {
            if ($i == 0) {
                if (is_int($value) OR is_bool($value)) {
                    $req = $req . intval($value);
                } elseif (is_array($value)) {
                    $req = $req . "'" . json_encode($value) . "'";
                } else {
                    $req = $req . "'" . $value . "'";
                }
            } else {
                if (is_int($value) OR is_bool($value)) {
                    $req = $req . ", " . intval($value);
                } elseif (is_array($value)) {
                    $req = $req . ", '" . json_encode($value) . "'";
                } else {
                    $req = $req . ", '" . $value . "'";
                }
            }
            $i++;
        }
        $req = $req . ")";
        return $req;
    }

    public function hydrateBDD($data)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($value)) {
                $this->$key = intval($value);
            } else {
                $newValue = json_decode($value);
                if ($newValue != NULL) {
                    $this->$key = $newValue;
                } else {
                    $this->$key = $value;
                }
            }
        }
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