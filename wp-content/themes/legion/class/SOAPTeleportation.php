<?php

include_once("item_home.php");

class SOAPTeleportation
{
    protected $soap;
    protected $online;
    public $message;

    public function __construct($map_id, $character, $currency = null)
    {
        $this->online = true;
        $this->done = false;
        $this->message = "";
        $item_home = new item_home();
        $characterIsOk = false;
        if (get_current_user_id() == 0) {
            $this->message = '<div style="display: inline-block;width: 100%;" class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>You must be connected !</strong>
</div>';
            return;
        }
        $characters = $item_home->getCharacters();
        foreach ($characters as $thisCharacter) {
            if ($thisCharacter["name"] == $character) {
                $characterIsOk = true;
                $character = $thisCharacter;
            }
        }
        if ($characterIsOk == true) {
            $map = $_SESSION["map"]->search($_SESSION["map"], $map_id);
            if ($map->getId() > 0) {
                $command = 'tele name ' . $character["name"] . ' ' . $map->name;
                if (($character["level"] < 110 AND $map->isCity == 1) OR ($character["level"] >= $map->minLevel AND $character["level"] <= $map->maxLevel)) {
                    $this->soapConnect();
                    $this->soapCommand($command);
                    if (isWowAdmin()) {
                        $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_teleport\',null,null,NOW(),' . get_current_user_id() . ',1,' . json_encode($command) . ',1)';
                    } else {
                        $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_teleport\',null,null,NOW(),' . get_current_user_id() . ',1,' . json_encode($command) . ',0)';
                    }
                    $GLOBALS["dbh"]->query($req);
                } else {
                    $req = "";
                    if ($currency == "buy") {
                        $priceBuy = $map->getPrice("buy");
                        $currentBuyPoints = get_user_meta(get_current_user_id(), 'buy_points');
                        if ($currentBuyPoints >= $priceBuy) {
                            $this->soapConnect();
                            $this->soapCommand($command);
                            if (!isWowAdmin()) {
                                removeBuyPoint(get_current_user_id(), $priceBuy);
                            }
                            $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_teleport\',null,' . $priceBuy . ',NOW(),' . get_current_user_id() . ',1,' . json_encode($command) . ',0)';
                        }
                        if (isWowAdmin()) {
                            $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_teleport\',null,null,NOW(),' . get_current_user_id() . ',1,' . json_encode($command) . ',1)';
                        }
                        $GLOBALS["dbh"]->query($req);
                    } elseif ($currency == "vote") {
                        $priceVote = $map->getPrice("vote");
                        $currentVotePoints = get_user_meta(get_current_user_id(), 'vote_points');
                        if ($currentVotePoints >= $priceVote) {
                            $this->soapConnect();
                            $this->soapCommand($command);
                            if (!isWowAdmin()) {
                                removeVotePoint(get_current_user_id(), $priceVote);
                            }
                            $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_teleport\',' . $priceVote . ',null,NOW(),' . get_current_user_id() . ',1,' . json_encode($command) . ',0)';
                        }
                        if (isWowAdmin()) {
                            $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_teleport\',null,null,NOW(),' . get_current_user_id() . ',1,' . json_encode($command) . ',1)';
                        }
                        $GLOBALS["dbh"]->query($req);
                    } else {
                        $this->message = '<div style="display: inline-block;width: 100%;" class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error ! Please reload the page</strong>
</div>';
                    }
                }
            } else {
                $this->message = '<div style="display: inline-block;width: 100%;" class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Invalid Map</strong>
</div>';
            }
        } else {
            $this->message = '<div style="display: inline-block;width: 100%;" class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error of character please reload the page</strong>
</div>';
        }
    }

    protected function soapConnect()
    {
        $this->soap = new SoapClient(NULL, Array(
            'location' => 'http://' . SOAP_IP . ':' . SOAP_PORT . '/',
            'uri' => 'urn:TC',
            'style' => SOAP_RPC,
            'login' => SOAP_USER,
            'password' => SOAP_PASS,
            'keep_alive' => false //keep_alive only works in php 5.4.
        ));
    }

    protected function soapCommand($command)
    {
        try {
            $this->soap->executeCommand(new SoapParam($command, 'command'));
        } catch (Exception $e) {
            $this->online = false;
        }
    }

    /**
     * @return boolean
     */
    public function isOnline()
    {
        return $this->online;
    }
}