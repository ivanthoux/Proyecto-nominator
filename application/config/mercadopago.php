<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Para obtener las credenciales 
| https://www.mercadopago.com.ar/developers/es/
|
| https://www.mercadopago.com.ar/developers/es/solutions/payments/basic-checkout/receive-payments/
|
*/

// Custom Checkout
$config['app_id'] = ''; // not used by the Library
$config['public_key'] = 'TEST-f3425669-c04b-403b-b1c1-7efabfbbc123';  // not used by the Library
$config['access_token'] = 'TEST-1043637943345419-063022-a48a660b4327e553fa2f513d378fe949-117340429';
$config['use_access_token'] = TRUE; // TRUE or FALSE

// Basic Checkout
$config['client_id'] = '';
$config['client_secret'] = '';

// Sandbox
$config['sandbox_mode'] = TRUE; // TRUE or FALSE

// Callback Return
$config['callback_success'] = ''; 
$config['callback_failure'] = ''; 
$config['callback_pending'] = ''; 
$config['auto_return'] = 'approved'; 

// Notification url
$config['notification_url'] = ENVIRONMENT_URL.'Api/mercadopago'; // TRUE or FALSE


/* End of file mercadopago.php */
/* Location: ./application/config/mercadopago.php */