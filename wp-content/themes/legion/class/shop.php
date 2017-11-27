<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 27/11/2017
 * Time: 18:36
 */

class shop
{
    private $array = null;

    public function getArray()
    {
        return $this->array;
    }

    public function erase()
    {
        $this->array = null;
    }
}