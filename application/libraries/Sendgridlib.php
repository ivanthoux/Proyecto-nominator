<?php

defined('BASEPATH') or exit('No direct script access allowed');

require("sendgrid-php/sendgrid-php.php");

class Sendgridlib
{

    var $SG_instance;

    public function __construct()
    {
        //torresrodrigoe sendgrid account
        $apiKey = getenv('SENDGRID_API_KEY');
    }

    public function send($emaildata)
    {
        try {
            return $this->SG_instance->send($emaildata);
        } catch (\SendGrid\Exception $e) {
            /*echo $e->getCode() . "\n";
            foreach ($e->getErrors() as $er) {
                echo $er;
            }*/
        }
    }
}
