<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Connection APIs for TeleProm
 *
 * @author harlyman
 */
class Teleprom
{

    /*
     * User
     */
    protected $_USER = 'nominator';
    /*
     * Password
     */
    protected $_PASSWORD = 'nominator';
    /*
     * URL host for Authorization
     */
    protected $_HOST = 'http://mayten.cloud/';
    /*
     * URL for send mesages
     */
    protected $_HOST_API = 'http://mayten.cloud/api/';

    public function __construct()
    {
    }

    /**
     * @return string Operation results
     */
    public function getToken()
    {
        $ch = curl_init($this->_HOST . "auth");
        $data = json_encode(array("username" => $this->_USER, "password" => $this->_PASSWORD));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );

        $remote_server_output = curl_exec($ch);

        $info = curl_getinfo($ch);
        if ($info['http_code'] != 200) {
            return null;
        }

        curl_close($ch);
        $token = json_decode($remote_server_output);
        return $token;
    }

    /**
     * @param string $token Token for Authorization
     * @param array $messages Array(mensaje, telefono, identificador)
     * 
     * @return boolean Operation results
     */
    public function sendMessagesShort($token, $messages)
    {
        $ch = curl_init($this->_HOST_API . "Mensajes/Texto");
        $data = json_encode(array("origen" => 'SMS_CORTO', 'mensajes' => $messages));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer '.$token,
                'Content-Length: ' . strlen($data)
            )
        );

        curl_exec($ch);

        $info = curl_getinfo($ch);
        if ($info['http_code'] != 200) {
            return false;
        }

        curl_close($ch);
        return true;
    }
}
