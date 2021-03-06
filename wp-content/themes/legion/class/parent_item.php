<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 15/11/2017
 * Time: 20:05
 */

class parent_item
{
    public $promotion = 0;
    public $id = null;
    public $name = null;
    public $time_promotion = 0;

    public function hydrateBDD($data)
    {
        foreach ($data as $key => $value) {
            if (is_int($value)) {
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
        if ($this->time_promotion < time()) {
            $this->promotion = 0;
        }
    }

    public function generateInsertRequest()
    {
        unset($this->id);
        if (is_a($this, 'item_set')) {
            unset($this->price);
        }
        $req = "INSERT INTO `" . get_class($this) . "`(";
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
                    $req = $req . "'" . addslashes(json_encode($value)) . "'";
                } else {
                    $req = $req . "'" . addslashes($value) . "'";
                }
            } else {
                if (is_int($value) OR is_bool($value)) {
                    $req = $req . ", " . intval($value);
                } elseif (is_array($value)) {
                    $req = $req . ", '" . addslashes(json_encode($value)) . "'";
                } else {
                    $req = $req . ", '" . addslashes($value) . "'";
                }
            }
            $i++;
        }
        $req = $req . ")";
        return $req;
    }

    public function getCharacters()
    {
        $characters = array();
        if (get_current_user_id() != 0) {
            $accountId = get_user_meta(get_current_user_id(), 'account_id')[0];
            $req = $GLOBALS["dbh"]->query("SELECT * FROM characters.characters WHERE account=" . $accountId);
            while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
                $character["name"] = $data["name"];
                $character["level"] = $data["level"];
                $character["class"] = getCharacterClassNameWithId($data["class"]);
                array_push($characters, $character);
            }
        }
        return $characters;
    }

    function getReduction($point, $type, $asInt, $showAsFree = false)
    {
        if ($this->promotion > 100 OR $this->promotion < 0) {
            $this->promotion = 0;
        }
        if ($type == "vote") {
            $realPrice = ($point * VOTE_POINTS);
        }
        if ($type == "buy") {
            $realPrice = ($point * BUY_POINTS);
        }
        $PriceReduction = $realPrice;
        if ($this->promotion > 0 AND $this->promotion <= 100 AND $this->time_promotion > time()) {
            $PriceReduction = $realPrice - ($realPrice * $this->promotion / 100);
        }
        if (is_a($this, 'item_set')) {
            $PriceReduction = $PriceReduction * 0.7;
        }
        if ($asInt == true) {
            return intval($PriceReduction);
        } else {
            if ($PriceReduction != $realPrice) {
                if ($showAsFree == true) {
                    return '<del>' . formatNumber(intval($realPrice)) . '</del> ' . formatNumber(0);
                } else {
                    return '<del>' . formatNumber(intval($realPrice)) . '</del> ' . formatNumber(intval($PriceReduction));
                }
            } else {
                if ($showAsFree == true) {
                    return formatNumber(0);
                } else {
                    return formatNumber(intval($realPrice));
                }
            }
        }
    }

    public function getVotePoint($dontShowReduction = false, $asInt = false, $showAsFree = false)
    {
        $realVotePrice = ($this->price * VOTE_POINTS);
        if ($dontShowReduction == true) {
            return intval($realVotePrice);
        }
        $realVotePrice = $this->getReduction($realVotePrice, "buy", $asInt, $showAsFree);
        return $realVotePrice;
    }

    public function getBuyPoint($dontShowReduction = false, $asInt = false, $showAsFree = false)
    {
        $realBuyPrice = ($this->price * BUY_POINTS);
        if ($dontShowReduction == true) {
            return intval($realBuyPrice);
        }
        $realBuyPrice = $this->getReduction($realBuyPrice, "buy", $asInt, $showAsFree);
        return $realBuyPrice;
    }
}