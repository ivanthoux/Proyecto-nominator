<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Connection APIs for Twillio
 *
 * @author harlyman
 */
class Twilio
{

  protected $API_URL = ENVIRONMENT === 'production' ? 'https://twilio.aguilasdevs.com' : 'https://twilio-dev.aguilasdevs.com';
  protected $APY_KEY = ENVIRONMENT === 'production' ? 'RRW3emN_eVQPr5J$Nj&qFc2mGpF&L3c3uKxv4WCwT@sL^CW$&7' : 'id8NWT*3scatxNh5Na38F5J7F';

  public function __construct()
  {
  }

  /**
   * Send Message
   * @param $toNumber string Number to send message to example: +543764123456
   * @param $message string Message to send
   * @param $type string Type of message only can by sms or whatsapp
   */
  public function send($toNumber, $message, $type)
  {
    // init conection POST
    $ch = curl_init($this->API_URL . '/messages/send');

    curl_setopt($ch, CURLOPT_HTTPHEADER, array('x-api-key: ' . $this->APY_KEY, "referer: {$_SERVER['HTTP_HOST']}"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("toNumber" => $toNumber, "message" => $message, "type" => $type)));

    $output = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_errno > 0) {
      echo "<p>codigoError: $curl_errno<br>mensajeError: $curl_error<p>";
    } else {
      $response = json_decode($output);
      if ($response->status === 'success') {
        return true;
      }
      echo $output;
    }
  }
}
