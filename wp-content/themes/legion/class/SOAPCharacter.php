<?php

class SOAPCharacter
{
    protected $soap;
    protected $online;
    public $message;

    public function __construct($character_selected, $item_set_id, $currency, $item_home_character)
    {
        $this->online = true;
        $this->message = "";
        $item_home = new item_home();
        $character_is_ok = false;
        $allCharacters = $item_home->getCharacters();
        foreach ($allCharacters as $character) {
            if ($character["name"] == $character_selected) {
                $character_is_ok = true;
                break;
            }
        }
        $item_set = getItemSetInBdd($item_set_id);
        if (($character_is_ok == false) AND ($item_set->item_set_id == null) AND ($currency != "buy" OR $currency != "vote")) {
            $this->message = '<div class="col-xs-12"><div style="display: inline-block;width: 100%;" class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error ! Please reload the page</strong>
</div></div>';
            return;
        }
        $haveEnoughtPoints = false;
        $votePoint = get_user_meta(get_current_user_id(), 'vote_points');
        $buyPoint = get_user_meta(get_current_user_id(), 'buy_points');
        $priceVote = $item_home_character->price * VOTE_POINTS;
        $priceBuy = $item_home_character->price * BUY_POINTS;
        if ($currency == "buy") {
            if ($buyPoint >= $priceBuy) {
                $haveEnoughtPoints = true;
            }
        } elseif ($currency == "vote") {
            if ($votePoint >= $priceVote) {
                $haveEnoughtPoints = true;
            }
        }
        if ($haveEnoughtPoints == false) {
            $this->message = '<div class="col-xs-12"><div style="display: inline-block;width: 100%;" class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>You don\'t have enought points</strong>
</div></div>';
            return;
        }
        $this->soapConnect();
        if ($this->online) {
            $allCommand = array();
            $command = 'character level ' . $character_selected . ' 110';
            $req = "";
            array_push($allCommand, $command);
            $this->soapCommand($command);
            foreach ($item_set->items as $item) {
                $command = 'send items ' . $character_selected . ' "Shop" "Shop" ' . $item . ':1';
                array_push($allCommand, $command);
                $this->soapCommand($command);
            }
            if (!isWowAdmin()) {
                $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_character\',null,null,NOW(),' . get_current_user_id() . ',1,\'' . json_encode($allCommand) . '\',1)';
            } else {
                if ($currency == "vote") {
                    removeVotePoint(get_current_user_id(), $priceVote);
                    $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_character\',' . $priceVote . ',null,NOW(),' . get_current_user_id() . ',1,\'' . json_encode($allCommand) . '\',0)';
                } elseif ($currency == "buy") {
                    removeBuyPoint(get_current_user_id(), $priceBuy);
                    $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_character\',null,' . $priceBuy . ',NOW(),' . get_current_user_id() . ',1,\'' . json_encode($allCommand) . '\',0)';
                }
            }
            $GLOBALS["dbh"]->query($req);
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