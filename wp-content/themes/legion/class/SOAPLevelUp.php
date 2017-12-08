<?php

class SOAPLevelUp
{
    protected $soap;
    protected $online;
    public $message;

    public function __construct($character, $level, $price, $currency)
    {
        $this->online = true;
        $this->message = "";
        $this->soapConnect();
        if (get_current_user_id() == 0) {
            $this->message = '<div style="display: inline-block;width: 100%;" class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>You must be connected !</strong>
</div>';
            return;
        }
        if ($this->online == true) {
            $command = 'character level ' . $character . ' ' . $level;
            $this->soapCommand($command);
            $req = "";
            if ($currency == "buy") {
                if (isWowAdmin()) {
                    $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_level\',null,null,NOW(),' . get_current_user_id() . ',1,' . json_encode($command) . ',1)';
                } else {
                    removeBuyPoint(get_current_user_id(), $price["buy"]);
                    $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_level\',null,' . $price["buy"] . ',NOW(),' . get_current_user_id() . ',1,' . json_encode($command) . ',0)';
                }
            } elseif ($currency == "vote") {
                if (isWowAdmin()) {
                    $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_level\',null,null,NOW(),' . get_current_user_id() . ',1,' . json_encode($command) . ',1)';
                } else {
                    removeVotePoint(get_current_user_id(), $price["vote"]);
                    $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_level\',' . $price["vote"] . ',null,NOW(),' . get_current_user_id() . ',1,' . json_encode($command) . ',0)';
                }
            } else {
                $this->message = '<div style="display: inline-block;width: 100%;" class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error ! Please reload the page</strong>
</div>';
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