<?php

class Contracts extends ManagerController
{

  protected $pathindex = '/contracts/all';
  protected $viewpath = 'manager/contracts/contract';

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
    $this->load->model(['Contract_model']);
    // $this->data['filters'] = $contractModel->get_filters();
    $this->data['filters'] = [];
    $viewpath = $this->viewpath . '_list';

    $this->data['activesidebar'] = 'contracts';

    $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/bootstrap-datepicker3.min.css'];
    // $this->dd($this->viewpath);
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);

    $this->datatables_assets();
    $this->_render($viewpath);
  }

  public function form($personId = false)
  {
    $this->load->model(['Contract_model', 'Persons_model']);
    $contractModel = new Contract_model();
    $personsModel = new Persons_model();

    $viewpath = $this->viewpath . '_form';

    $post = $this->input->post();
    if (!empty($post)) { //saving user received
      // $this->dd($post);
      $this->_checkConstrain($post);
      if (empty($this->data['errors'])) {
        $contractStart = DateTime::createFromFormat('d-m-Y', $post['contract_start']);
        $post['contract_start'] = $contractStart->format('Y-m-d');
        $contractEnd = DateTime::createFromFormat('d-m-Y', $post['contract_end']);
        $post['contract_end'] = $contractEnd->format('Y-m-d');
        $personId = $post['contract_person'];
        $id = $contractModel->save($post);
        redirect('/persons/form/' . $personId, 'refresh');
      }
    }
    if (!empty($personId)) {

      $this->data['person'] = $personsModel->get_by_id($personId);
    }

    $this->data['activesidebar'] = 'clients';

    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
    $this->javascript[] = ['url' => base_url() . MAN_JS . 'libs/jquery.mask.min.js'];
    $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js'];

    $this->_render($this->viewpath . '_form');
  }

  public function remove($contract_id)
  {
      $this->load->model('Contract_model');
      $this->Contract_model->delete($contract_id);
      redirect('/persons', 'refresh');
  }

  public function datatables()
  {
    $this->load->model('Contract_model');
    $contractModel = new Contract_model();
    echo json_encode($contractModel->datatables_ajax_list());
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

  private function _checkConstrain($post)
  {
    if (empty($post['contract_person'])){
      $this->data['errors'][] = '<p>Persona inexistente.</b></p>';  
    }
    if (empty($post['contract_start'])){
      $this->data['errors'][] = '<p>Debe ingresar una fecha de inicio de contrato.</b></p>';  
    }
    if (empty($post['contract_end'])){
      $this->data['errors'][] = '<p>Debe ingresar una fecha de fin de contrato.</b></p>';  
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
