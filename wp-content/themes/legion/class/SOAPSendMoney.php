<?php

include_once("item_home.php");

class SOAPSendMoney
{
    protected $soap;
    protected $online;
    public $message;

    public function __construct($character, $currency, $amount, $gold)
    {
        $this->online = true;
        $this->done = false;
        $this->message = "";
        $this->soapConnect();
        if ($this->online == true) {
            $command = '.send money ' . $character . ' "Money" "Money" ' . $gold * 10000;
            $this->soapCommand($command);
            $req = "";
            if ($currency == "buy") {
                if (!isWowAdmin()) {
                    removeBuyPoint(get_current_user_id(), $amount);
                    $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_gold\',null,' . $amount . ',NOW(),' . get_current_user_id() . ',1,' . json_encode($command) . ',0)';
                } else {
                    $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_gold\',null,null,NOW(),' . get_current_user_id() . ',1,' . json_encode($command) . ',1)';
                }
            } elseif ($currency == "vote") {
                if (!isWowAdmin()) {
                    removeVotePoint(get_current_user_id(), $amount);
                    $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_gold\',' . $amount . ',null,NOW(),' . get_current_user_id() . ',1,' . json_encode($command) . ',0)';
                } else {
                    $req = 'INSERT INTO `log_sells`(`item_id`, `item_set_id`, `item_home`, `vote_points`, `buy_points`, `date`, `user_id`, `quantity`, `command`, `admin`) VALUES (null,null,\'item_home_gold\',null,null,NOW(),' . get_current_user_id() . ',1,' . json_encode($command) . ',1)';
                }
            }
            $GLOBALS["dbh"]->query($req);
        } else {
            $this->message = '<div style="display: inline-block;width: 100%;" class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error ! Please reload the page</strong>
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