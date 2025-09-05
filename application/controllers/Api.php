<?php

use Guzzle\Http\Client;

require(APPPATH . '/libraries/REST_Controller.php');

class Api extends REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
    }

    private function dd($object)
    {
        echo '<pre>' . print_r($object, true) . '</pre>';
        die();
    }

    public function callbacksms_post()
    {
        $post = $this->post();
        if (!isset($post['fechaHora']) || !isset($post['identificador']) || !isset($post['resultado']) || !isset($post['empresa']))
            $this->response(null, 500);

        $this->load->model('Phonevalidations_model');
        $phonevalidation_model = new Phonevalidations_model();

        $validation = $phonevalidation_model->get_by_id($post['identificador']);
        if (!$validation) {
            $this->response(null, 404);
        }
        if ($validation['phonevalidation_state'] != null) {
            $this->response(null, 406);
        }
        if ($validation['phonevalidation_code'] == null) {
            $this->db->set('phonevalidation_validated_at', date('Y-m-d H:i:s'));
        }

        $state = null;
        switch ($post['resultado']) {
            case 'Envio_Exitoso':
                $state = 1;
                break;
            case 'Envio_Fallido':
                $state = 2;
                break;
            case 'incompatible':
                $state = 3;
                break;
            case 'Recibido':
                $state = 4;
                break;
        }

        $phonevalidation_model->save([
            'phonevalidation_id' => $post['identificador'],
            'phonevalidation_response_at' => $post['fechaHora'],
            'phonevalidation_state' => $state,
            'phonevalidation_statetext' => $post['resultado'],
            'phonevalidation_company' => $post['empresa'],
        ]);
        $this->response(null, 200);
    }

    public function creditssms_post()
    {
        $post = $this->post();
        if (!isset($post['fechaHora']))
            $this->response(null, 500);

        $this->load->model(['User_model', 'Settings_model']);

        $setting_model = new Settings_model();
        $setting_data = $setting_model->get_all();
        if ($setting_data) {
            $this->_sendAlertSMS(json_decode($setting_data[0]['setting_data'], true));
        } else {
            $this->_sendAlertSMS(null);
        }
    }

    private function _sendAlertSMS($setting)
    {
        $user_model = new User_model();
        $emails = $user_model->get_emails_by_role('super');

        $body = $this->load->view('mails/alertsms', [], true);

        if (empty($setting) || !isset($setting['mail_sms']) || $setting['mail_sms']) {
            send_email([
                'to' => $emails,
                'subject' => 'Alerta de crédito SMS nominator.com.ar',
                'message' => $body,
            ]);
        }
    }

    public function mercadopago_post()
    {
        $this->load->model(['Client_model', 'ClientPeriod_model', 'User_model']);
        $client_model = new Client_model();
        $clientperiod_model = new ClientPeriod_model();
        $user_model = new User_model();

        $user = $user_model->getSYSTEM();
        $this->session->set_userdata($user);

        $this->load->library('Utils');
        $utils = new Utils();

        $this->config->load("mercadopago", TRUE);
        $config = $this->config->item('mercadopago');
        $this->load->library('Mercadopago', $config);
        $MP = new Mercadopago($config);
        // $accesToken = $MP->get_access_token();

        $post = $this->post();
        // $this->db->insert('mercado_pago', ['data' => json_encode($post), 'type' => 'POST']);

        // if (isset($post['topic']) && $post['topic'] === 'merchant_order') {
        //     // $this->dd($config);
        //     $order = $MP->get(str_replace('https://api.mercadolibre.com', '', $post['resource']));
        //     $this->db->insert('mercado_pago', ['data' => json_encode($order), 'type' => 'merchant_order']);
        // }
        if (isset($post['type']) && $post['type'] === 'payment') {
            // $this->dd($config);
            // $this->db->insert('mercado_pago', ['data' => '/' . $post['api_version'] . '/payments/' . $post['data']['id'] . '?access_token=' . $accesToken, 'type' => 'payment']);
            $pay = $MP->get('/' . $post['api_version'] . '/payments/' . $post['data']['id']);
            if ($pay['response']) {
                $this->db->insert('mercado_pago', [
                    'json' => json_encode($pay['response']),
                    'payment_id' => $post['data']['id'],
                    'date_created' => $pay['response']['date_created'],
                    'status' => $pay['response']['status'],
                    'status_detail' => $pay['response']['status_detail'],
                    'client_id' => $pay['response']['external_reference'],
                    'transaction_amount' => $pay['response']['transaction_amount'],
                    'payment_method_id' => $pay['response']['payment_method_id'],
                    'payment_type_id' => $pay['response']['payment_type_id']
                ]);
                if ($pay['response']['status'] === 'approved') {
                    // if ($pay['response']['status'] === 'approved') {
                    $client = $client_model->get_by_id($pay['response']['external_reference'], true);
                    $periods = $clientperiod_model->get_client_unpaid($pay['response']['external_reference'], false, false, false, ['2']);
                    // $this->dd($periods);
                    $row = [
                        'pay_type' => 'MercadoPago',
                        'emision' => $pay['response']['date_created'],
                        'importe_comprobante' => $pay['response']['transaction_amount'] * -1
                    ];
                    // $this->dd($client);
                    $utils->importPay($row, false, $periods, $client);
                }
            }
            // }
        }

        $this->response(null, 200);
    }
}
