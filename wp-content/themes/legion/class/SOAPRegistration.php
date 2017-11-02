<?php

class SOAPRegistration
{
    protected $soap;
    protected $created;

    public function __construct($mail, $password)
    {
        $this->created=true;
        $this->soapConnect();
        $this->soapCommand('bnetaccount create ' . $mail . ' ' . $password);
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
            $this->created=false;
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }
}