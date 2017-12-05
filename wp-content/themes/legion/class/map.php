<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 05/12/2017
 * Time: 18:16
 */

class map
{
    private $id;
    public $name;
    public $url;
    private $children;
    public $topPos;
    public $leftPos;
    public $width;
    public $height;
    public $isCity;
    public $minLevel;
    public $maxLevel;
    public $canTp;

    function __construct()
    {
        $this->id = null;
        $this->name = null;
        $this->url = null;
        $this->children = array();
        $this->topPos = 0;
        $this->leftPos = 0;
        $this->width = 10;
        $this->height = 10;
        $this->isCity = null;
        $this->minLevel = null;
        $this->maxLevel = null;
        $this->canTp = null;
    }
}