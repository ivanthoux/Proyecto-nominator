<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Home extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['pageName'] = 'home';
        $this->load->model(array('Settings_model'));
        $settings = $this->session->userdata('settings');
        $this->data['settings'] = $settings;
        $this->template = 'default';

        if ($this->session->userdata('user_logged')) {
            redirect('/manager', 'refresh');
        }
    }

    public function index()
    {
        $this->load->model([
            'Client_model',
            'ClientPack_model',
            'Payment_model',
            'Pack_model',
            'ClientPeriod_model'
        ]);
        $this->load->library('encrypt');

        $get = $this->input->get();
        // if (!isset($get['token'])) {
        //     return $this->_render('default/index');
        // }

        $post = $this->input->post();
        if (!empty($post) && isset($post['dni']) && !empty($post['dni'])) {
            // $this->dd($post);
            $client_model = new Client_model();
            $clientpack_model = new ClientPack_model();
            $payment_model = new Payment_model();
            $pack_model = new Pack_model();
            $clientperiod_model = new ClientPeriod_model();

            // $this->dd($get['token']);
            // $token = $this->encrypt->decode($get['token']);
            // $this->dd($token);
            // $token = json_decode($token, true);

            $this->db->where("(CAST(client_doc AS UNSIGNED) = " . $post['dni'] . " OR client_cuil = '" . $post['dni'] . "')", null, false);
            $client = $client_model->get_all();
            if (!$client) {
                $this->data['errors'] = 'El D.N.I./C.U.I.T. no coincide con el de un Cliente';
                return $this->_render('default/index');
            }
            $this->data['client'] = $client[0];
            $this->data['packs'] = [];
            $this->data['date'] = date('Y-m-d');

            $periods = $clientperiod_model->get_client_unpaid($this->data['client']['client_id'], false, false, $this->data['date']);

            foreach ($periods as $key => $period) {
                $last_pay = $payment_model->get_by_clientperiod($period['clientperiod_id']);
                if (!empty($last_pay)) {
                    $last_pay = $last_pay[0];
                    if (date('U', strtotime($periods[$key]['clientperiod_date'])) < date('U', strtotime($last_pay['pay_date']))) {
                        $period['clientperiod_date'] = $last_pay['pay_date'];
                    }
                }

                $clientpack = $clientpack_model->get_by_id($period['clientperiod_pack']);
                $pack = $pack_model->get_by_id($clientpack['clientpack_package']);

                $days = ((new DateTime($this->data['date']))->diff(new DateTime($period['clientperiod_date'])))->days;

                $amount = round($period['clientperiod_amount'], 2);
                $punitorios = round(($days * ($pack['pack_daytask'] / 100)) * $amount, 2);

                $found = false;
                $idx = 0;
                foreach ($this->data['packs'] as $key => $row) {
                    if ($row['clientpack']['clientpack_id'] == $clientpack['clientpack_id']) {
                        $found = true;
                        $idx = $key;
                    }
                }
                if (!$found) {
                    $this->data['packs'][] = [
                        'pack' => $pack,
                        'clientpack' => $clientpack,
                        'periods' => [[
                            'session' => $period['clientperiod_packperiod'],
                            'date' => $period['clientperiod_date'],
                            'amount' => $amount,
                            'punitorios' => $punitorios
                        ]]
                    ];
                } else {
                    $this->data['packs'][$idx]['periods'][] = [
                        'session' => $period['clientperiod_packperiod'],
                        'date' => $period['clientperiod_date'],
                        'amount' => $amount,
                        'punitorios' => $punitorios
                    ];
                }
            }
        }

        $this->javascript[] = $this->load->view('default/index_js', $this->data, true);
        $this->_render('default/index');
    }

    public function linkMP()
    {
        $this->config->load("mercadopago", TRUE);
        $config = $this->config->item('mercadopago');
        $this->load->library('Mercadopago', $config);
        
        // $this->dd($config);
        $MP = new Mercadopago($config);
        $payload = [
            'external_reference' => 'CUIT_CLIENTE',
            "expires" => true,
            "expiration_date_from" =>  date('c'),//"2017-02-01T12:00:00.000-04:00",
            "expiration_date_to" =>  date('c', strtotime('2 minutes')),
            'items' => [[
                'title' => 'Titulo del PAGO',
                'description' => 'Descripción del PAGO',
                'quantity' => 1,
                'unit_price' => 125.32
            ]]
        ];
        // $this->dd($payload);
        // $accesToken = $MP->get_access_token();
        // $this->dd($accesToken);
        $preference = $MP->create_preference($payload);
        $this->dd($preference);
        // $preference = $MP->get_preference($preference['response']['id']);
        // $preference = $MP->get_preference('117340429-1bc2b6aa-9122-495f-8ee7-753553c87b45');
        // $this->dd($preference);
    }
}
