<?php

class SOAPCharacter
{
    protected $soap;
    protected $online;
    public $message;

    public function __construct($character_selected, $item_set_id, $currency)
    {
        $this->online = true;
        $this->message = "";
        //faire vérif
        $this->soapConnect();
        if ($this->online) {
            //utiliser les différents soap sans enlever le prix en l'enregistrant dans les logs comme ietm_home character
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