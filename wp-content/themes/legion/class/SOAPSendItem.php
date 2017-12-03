<?php

class SOAPSendItem
{
    protected $soap;
    protected $online;
    public $message;

    public function __construct($item, $quantity, $point_vote, $point_buy, $character, $type)
    {
        $this->online = true;
        $this->done = false;
        $this->message = "";
        $this->soapConnect();
        if ($type == 'item') {
            $command = 'send items ' . $character . ' "Shop" "Shop" ' . $item . ':' . $quantity;
            $this->soapCommand($command);
            if ($this->online == true) {//command has been execute
                $req = "";
                if ($point_vote == null) {
                    $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`) VALUES (' . $item . ',null,null,null,' . $point_buy . ',NOW(),' . get_current_user_id() . ',' . $quantity . ',\'' . json_encode($command) . '\')';
                } elseif ($point_buy == null) {
                    $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`) VALUES (' . $item . ',null,null,' . $point_vote . ',null,NOW(),' . get_current_user_id() . ',' . $quantity . ',\'' . json_encode($command) . '\')';
                }
                $GLOBALS["dbh"]->query($req);
                $this->message = "<div class=\"alert alert-success\"><strong>Item " . $item . " has been send to " . $character . "</strong></div>";
                $_SESSION["shop"]->erase($item, $type);
                if ($point_buy == null) {
                    removeVotePoint(get_current_user_id(), $point_vote);
                } else {
                    removeBuyPoint(get_current_user_id(), $point_buy);
                }
            }
        } elseif ($type == "item_set") {
            foreach ($item->items as $item_id) {
                $command = 'send items ' . $character . ' "Shop" "Shop" ' . $item_id . ':' . $quantity;
                $this->soapCommand($command);
            }
            if ($this->online == true) {//command has been execute
                $req = "";
                if ($point_vote == null) {
                    $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`) VALUES (null,' . $item->item_set_id . ',null,null,' . $point_buy . ',NOW(),' . get_current_user_id() . ',' . $quantity . ',\'' . json_encode($command) . '\')';
                } elseif ($point_buy == null) {
                    $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`) VALUES (null,' . $item->item_set_id . ',null,' . $point_vote . ',null,NOW(),' . get_current_user_id() . ',' . $quantity . ',\'' . json_encode($command) . '\')';
                }
                $GLOBALS["dbh"]->query($req);
                $this->message = "<div class=\"alert alert-success\"><strong>Item set " . $item->item_set_id . " has been send to " . $character . "</strong></div>";
                $_SESSION["shop"]->erase($item->item_set_id, $type);
                if ($point_buy == null) {
                    removeVotePoint(get_current_user_id(), $point_vote);
                } else {
                    removeBuyPoint(get_current_user_id(), $point_buy);
                }
            }
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