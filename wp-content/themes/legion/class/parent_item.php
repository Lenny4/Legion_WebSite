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
        $accountId = get_user_meta(get_current_user_id(), 'account_id')[0];
        $req = $GLOBALS["dbh"]->query("SELECT * FROM characters.characters WHERE account=" . $accountId);
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $character["name"] = $data["name"];
            $character["level"] = $data["level"];
            array_push($characters, $character);
        }
        return $characters;
    }

    public function getVotePoint()
    {
        if ($this->promotion > 100 OR $this->promotion < 0) {
            $this->promotion = 0;
        }
        $realVotePrice = ($this->price * VOTE_POINTS);
        $PricePromotion = $realVotePrice - ($realVotePrice * $this->promotion / 100);
        if ($this->promotion > 0 AND $this->promotion <= 100) {
            return '<del>' . intval($realVotePrice) . '</del>' . intval($PricePromotion);
        } else {
            return intval($realVotePrice);
        }
    }

    public function getBuyPoint()
    {
        if ($this->promotion > 100 OR $this->promotion < 0) {
            $this->promotion = 0;
        }
        $realBuyPrice = ($this->price * BUY_POINTS);
        $PricePromotion = $realBuyPrice - ($realBuyPrice * $this->promotion / 100);
        if ($this->promotion > 0 AND $this->promotion <= 100) {
            return '<del>' . intval($realBuyPrice) . '</del>' . intval($PricePromotion);
        } else {
            return intval($realBuyPrice);
        }
    }
}