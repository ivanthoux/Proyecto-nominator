<?php

if (!function_exists('send_email')) {

    function send_email($data) {
        $CI = & get_instance();
        $CI->load->library('Sendgridlib');

        $data['to'] = strpos($_SERVER['SERVER_NAME'], 'localhost') !== FALSE ? 'silveiradeandrade.carlos@gmail.com' : $data['to'];

        $email = new SendGrid\Email();

        if (is_array($data['to'])) {
          foreach ($data['to'] as $to) {
            $email->addTo($to);
          }
        } else {
          $email->addTo($data['to']);
        }
    
        if (isset($data['cc'])) {
          $data['cc'] = strpos($_SERVER['SERVER_NAME'], 'localhost') !== FALSE ? 'harlyman.facebook@gmail.com' : $data['cc'];
          if (is_array($data['cc'])) {
            foreach ($data['cc'] as $cc) {
              $email->addCc($cc);
            }
          } else {
            $email->addCc($data['cc']);
          }
        }
    
        if (isset($data['files'])) {
          $email->setAttachments($data['files']);
        }
    
        $email->setFromName(!empty($_SESSION['settings']['name']) ? $_SESSION['settings']['name'] : 'Sistema')
                ->setFrom(!empty($_SESSION['settings']['email']) ? $_SESSION['settings']['email'] : 'noresponder@nominator.com.ar')
                ->setSubject($data['subject'])
                ->setHtml($data['message']);

        $CI->sendgridlib->send($email);
    }

}
