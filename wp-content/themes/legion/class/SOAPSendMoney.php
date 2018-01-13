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
            $this->soapCommand('.send money ' . $character . ' "Money" "Money" ' . $gold * 10000);
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