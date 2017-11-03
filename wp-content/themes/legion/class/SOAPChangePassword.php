<?php

class SOAPChangePassword
{
    protected $soap;

    public function __construct($mail,$newPassword)
    {
        $this->soapConnect();
        $this->soapCommand('bnetaccount set password ' . $mail . ' ' . $newPassword . ' ' . $newPassword . '');
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
            return false;
        }
        return true;
    }
}