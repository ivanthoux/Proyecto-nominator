<?php

class Clients extends ManagerController
{

  protected $pathindex = '/clients/all';
  protected $viewpath = 'manager/clients/client';

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    redirect($this->pathindex, 'refresh');
  }

  public function all()
  {
    $this->load->model(['Client_model']);
    $client_model = new Client_model();

    $viewpath = $this->viewpath . '_list';

    $this->data['filters'] = $client_model->get_filters();
    $this->data['activesidebar'] = 'clients';

    $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/bootstrap-datepicker3.min.css'];
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);

    $this->datatables_assets();
    $this->_render($viewpath);
  }

  public function form($_id = false)
  {
    $this->load->model(['Client_model', 'Regions_model', 'Cities_model']);
    $client_model = new Client_model();
    $regions_model = new Regions_model();

    $viewpath = $this->viewpath . '_form';

    $post = $this->input->post();
    if (!empty($post)) { //saving user received
      // $this->dd($post);
      unset($post['childs']);
      $this->_checkConstrain($post);
      if (empty($this->data['errors'])) {
        $post['client_city'] = !empty($post['client_city']) ? $post['client_city'] : null;
        $post['client_mobile_validate'] = !empty($post['client_mobile_validate']) ? $post['client_mobile_validate'] : null;
        $post['client_ref1_phone_validate'] = !empty($post['client_ref1_phone_validate']) ? $post['client_ref1_phone_validate'] : null;

        $id = $client_model->save($post);
        if (empty($post['client_id'])) {
          $post['client_id'] = $id;
          $this->logCreate($client_model, $post, 'c');
        } else {
          $this->logCreate($client_model, $post, 'u');
        }
        redirect('/clientpacks/form/' . $id, 'refresh');
      }
    }
    if (!empty($_id)) {
      $this->data['edit'] = $client_model->get_by_id($_id);

      $this->data['edit']['region_id'] = null;
      if ($this->data['edit']['client_city']) {
        $cities_model = new Cities_model();
        $city = $cities_model->get_by_id($this->data['edit']['client_city']);
        $this->data['edit']['region_id'] = $city['region_id'];
      }
    }

    $this->data['activesidebar'] = 'clients';
    $this->data['regions'] = $regions_model->get_all();

    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
    $this->javascript[] = ['url' => base_url() . MAN_JS . 'libs/jquery.mask.min.js'];

    $this->_render($this->viewpath . '_form');
  }

  public function datatables()
  {
    $this->load->model('Client_model');
    $client_model = new Client_model();
    echo json_encode($client_model->datatables_ajax_list());
  }

  public function desactive($_id)
  {
    $this->load->model('Client_model');
    $this->checkPermission('desactive_clientprice');
    $data = $this->Client_model->get_by_id($_id, true);
    $data['client_active'] = 0;
    $this->Client_model->save($data);
    $this->logCreate($this->Client_model, $data, 'u');
    redirect($this->pathindex, 'refresh');
  }

  public function active($_id)
  {
    $this->load->model('Client_model');
    $this->checkPermission('active_clientprice');
    $data = $this->Client_model->get_by_id($_id, true);
    $data['client_active'] = 1;
    $this->Client_model->save($data);
    $this->logCreate($this->Client_model, $data, 'u');
    redirect($this->pathindex, 'refresh');
  }

  private function _checkConstrain($client)
  {
    if (empty(trim($client['client_doc']))) {
      $this->data['errors'][] = '<p>Formato de Código de cliente invalido</p>';
    } else {
      $c = $this->Client_model->get_where(['client_doc' => $client['client_doc']]);
      //        $this->dd($c);
      if (!empty($c)) {
        if ($c[0]['client_id'] != $client['client_id']) {
          $this->data['errors'][] = '<p>Ya existe un cliente con este número de Cliente: <b>' . $c[0]['client_firstname'] . ' ' . $c[0]['client_lastname'] . '</b></p>';
        }
      }
    }
    if (!empty($client['client_email']) && !filter_var($client['client_email'], FILTER_VALIDATE_EMAIL)) {
      $this->data['errors'][] = '<p>Formato de E-mail inválido</b></p>';
    }
  }

  public function setCodeValidate($phone, $type)
  {
    $this->load->model(['Phonevalidations_model', 'Phonevalidationsession_model', 'Client_model', 'Settings_model', 'User_model']);
    $this->load->library(['Teleprom']);
    $sms = new Teleprom();

    $token = false;

    $phonevalidationsession_model = new Phonevalidationsession_model();
    $session = $phonevalidationsession_model->getLast();
    if (!$session) {
      $token = $sms->getToken();
      $phonevalidationsession_model->save([
        'token' => $token->token,
        'expiration' => date('Y-m-d H:i:s', strtotime($token->expires))
      ]);
    } else if (strtotime(date('Y-m-d H:i:s')) >= strtotime('-1 minutes', strtotime($session['expiration']))) {
      $token = $sms->getToken();
      $phonevalidationsession_model->save([
        'id' => $session['id'],
        'token' => $token->token,
        'expiration' => date('Y-m-d H:i:s', strtotime($token->expires))
      ]);
    } else {
      $token = (object) [
        'token' => $session['token']
      ];
    }

    if ($token) {
      $code = rand(10000, 99999);

      $phonevalidations_model = new Phonevalidations_model();
      $id = $phonevalidations_model->save([
        'phonevalidation_id' => null,
        'phonevalidation_phone' => '54' . $phone,
        'phonevalidation_code' => ($type !== 'confirm' ? null : $code),
        'phonevalidation_created_at' => date('Y-m-d H:i:s'),
        'phonevalidation_created_by' => $this->session->userdata('user_id')
      ]);

      $msg = 'nominator PIN: ' . $code . "\n Fecha: " . date('d/m/Y H:i:s');
      if ($type !== 'confirm') {
        $client_model = new Client_model();
        $client = $client_model->get_by_id($type);
        $msg = $client['client_firstname'] . ' ' . $client['client_lastname'] . ' acaba de registrarte como referencia para un crédito en nominator';
      }

      $message = array(
        array(
          'mensaje' => $msg,
          'telefono' => '54' . $phone,
          'identificador' => $id
        )
      );

      if ($sms->sendMessagesShort($token->token, $message)) {
        $setting_model = new Settings_model();
        $setting_data = $setting_model->get_all();
        if ($setting_data) {
          $setting = json_decode($setting_data[0]['setting_data'], true);
          if (isset($setting['credits_sms'])) {
            if ($setting['credits_sms'] != 0) {
              $setting['credits_sms'] = $setting['credits_sms'] - 1;

              if ($setting['credits_sms'] == $setting['alert_sms']) {
                $this->_sendAlertSMS($setting);
              }
              $setting_data[0]['setting_data'] = json_encode($setting);
              $setting_model->save($setting_data[0]);
            } else {
              $this->_sendAlertSMS($setting);
            }
          } else {
            $this->_sendAlertSMS($setting);
          }
        } else {
          $this->_sendAlertSMS(null);
        }

        echo json_encode(array('status' => 'success', 'id' => $id));
      } else {
        echo json_encode(array('status' => 'fail', 'message' => 'Error en el envío del código de validación, pongase en contacto con Administración'));
      }
    } else {
      echo json_encode(array('status' => 'fail', 'message' => 'El Token de seguridad no es válido, pongase en contacto con Administración'));
    }
  }

  public function getCodeValidate($phone)
  {
    $this->load->model(['Phonevalidations_model']);
    $phonevalidations_model = new Phonevalidations_model();
    $last = $phonevalidations_model->getLast($phone);
    echo json_encode(array('status' => !empty($last) ? 'success' : 'fail', 'id' => !empty($last) ? $last['phonevalidation_id'] : ''));
  }

  public function validateCodePhone($id, $code, $client = false)
  {
    $this->load->model(['Phonevalidations_model', 'Client_model']);

    $phonevalidations_model = new Phonevalidations_model();
    $client_model = new Client_model();

    $validate = $phonevalidations_model->validate($id, $code);
    if (!empty($client) && $client != 0) {
      $c = $client_model->get_by_id($client);
      if ($c) {
        $client_model->save([
          'client_id' => $client,
          'client_firstname' => $c['client_firstname'],
          'client_lastname' => $c['client_lastname'],
          'client_mobile_validate' => $id
        ]);
      }
    }

    echo json_encode(array('status' => $validate ? 'success' : 'fail'));
  }

  public function checkStatusSMS($id, $client = false)
  {
    $this->load->model(['Phonevalidations_model', 'Client_model']);

    $phonevalidations_model = new Phonevalidations_model();
    $client_model = new Client_model();

    $validate = $phonevalidations_model->get_by_id($id);
    if (!empty($client) && $client != 0) {
      $c = $client_model->get_by_id($client);
      if ($c) {
        $client_model->save([
          'client_id' => $client,
          'client_firstname' => $c['client_firstname'],
          'client_lastname' => $c['client_lastname'],
          'client_mobile_validate' => $id
        ]);
      }
    }

    echo json_encode(array('status' => $validate ? 'success' : 'fail', 'state' => $validate['phonevalidation_state']));
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

  public function getSearch()
  {
    $this->load->model(['Client_model']);
    $client_model = new Client_model();

    $get = $this->input->get();
    if ($get) {
      $road = $client_model->getSearch($get['search']['value']);
      $data = [];
      foreach ($road as $clientpack) {
        $data[] = [
          'id' => $clientpack['client_doc'],
          'name' => $clientpack['client_firstname'] . ' ' . $clientpack['client_doc']
        ];
      }
      echo json_encode($data);
    } else {
      echo [];
    }
  }
}
