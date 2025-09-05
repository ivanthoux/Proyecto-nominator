<?php

class Clientpacks extends ManagerController
{

  protected $pathindex = '/clientpacks/all';
  protected $viewpath = 'manager/clients/packs/clientpack';
  public $data = [];

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    redirect($this->pathindex, 'refresh');
  }

  public function all($client_id = false)
  {
    $this->load->model([
      'ClientPack_model',
      'Client_model',
    ]);
    $client_model = new Client_model();
    $clientpack_model = new ClientPack_model();
    // $clientpack_model->get_where(['clientpack_title' => 'asdfasdfasd']);

    $viewpath = $this->viewpath . '_list';

    // $this->dd($this->data);
    $this->data['client_id'] = $client_id;
    $this->data['filters'] = $clientpack_model->get_filters();
    if (!empty($client_id)) {
      $this->data['client'] = $client_model->get_by_id($client_id);
      $this->data['activesidebar'] = 'clients';
    } else {
      $this->data['activesidebar'] = 'clientpacks';
    }
    // $this->dd($this->data);

    $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/bootstrap-datepicker3.min.css'];
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);

    $this->datatables_assets();
    $this->_render($viewpath);
  }

  public function viewed($client, $_id)
  {
    $this->load->model(['ClientPack_model', 'Client_model']);
    $clientPack_model = new ClientPack_model();

    $data['clientpack_id'] = $_id;
    $data['clientpack_viewed'] = date('Y-m-d H:i:00');
    $clientPack_model->save($data);
    $this->logCreate($clientPack_model, $this->ClientPack_model->get_by_id($_id), 'u');
    redirect('/clientpacks/all/' . $client);
  }

  public function form($client = false, $_id = false)
  {
    $this->load->model([
      'Pack_model',
      'ClientPack_model',
      'ClientFile_model',
      'PackRules_model',
      'Client_model',
      'ClientPeriod_model'
    ]);
    $pack_model = new Pack_model();
    $clientPack_model = new ClientPack_model();
    $client_model = new Client_model();
    $clientPeriod_model = new ClientPeriod_model();

    $this->load->library('Utils');
    $util = new Utils();

    $viewpath = $this->viewpath . '_form';
    $this->data['activesidebar'] = 'clients';

    $this->data['client_id'] = $client;
    $this->data['client'] = $client_model->get_by_id($client);
    $this->data['client_active_periods'] = $clientPeriod_model->get_client_unpaid_by_product($client);

    if (!empty($this->data['client']['client_parent'])) {
      $this->data['parent'] = $client_model->get_by_id($this->data['client']['client_parent']);
    }

    $post = $this->input->post();
    if (!empty($post)) { //saving user received
      // $this->dd($post);
      $util->postForm($post, true, $this);
    } else { //render new user form or edit user
      if (!empty($_id)) {
        $this->data['edit'] = $clientPack_model->get_by_id($_id);
        $packrules = $clientPack_model->get_rules($_id);
        foreach ($packrules as $value) {
          $this->data['edit']['packrules']['packrule_' . $value['clientpackrule_packrule']] = htmlentities($value['clientpackrule_value'], ENT_NOQUOTES, 'UTF-8');
        }
      }
    }

    $this->data['clientpack_old'] = $clientPack_model->get_all(array('clientpack_client' => $client));
    $this->data['packages'] = [];

    $this->data['judicialized'] = $clientPack_model->isJudicialized($client);
    if (!$this->data['judicialized'] || !empty($_id)) {
      $this->data['packages'] = $pack_model->get_all(false, empty($_id), true);
      // $this->dd($this->data['packages']);
    }

    $this->css[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css'];
    $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js'];
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);

    $this->_render($this->viewpath . '_form');
  }

  public function receipt($client = false, $_id = false, $periods = false)
  {
    $this->load->library('Utils');
    $util = new Utils();
    $util->setController($this);
    $util->dataReceipt($client, $_id, $periods);

    $this->data['activesidebar'] = 'clients';
    $this->_render('manager/clients/packs/clientpack_receipt');
  }

  public function contract($client = false, $_id = false)
  {
    $this->load->library('Utils');
    $util = new Utils();

    $util->dataContract($client, $_id);

    $this->data['activesidebar'] = 'clients';
    $this->_render('manager/clients/packs/clientpack_contract');
  }

  public function datatables()
  {
    $this->load->model('ClientPack_model');
    $clientpack_model = new ClientPack_model();

    $data = $clientpack_model->datatables_ajax_list();
    $operatons = $clientpack_model->get_operations();

    $data['capital'] = money_formating($operatons['capital']);
    $data['capital_numbers'] = money_formating($operatons['capital_numbers'], true, false);

    $data['capital_liquid'] = money_formating($operatons['capital_liquid']);
    $data['capital_numbers_liquid'] = money_formating($operatons['capital_numbers_liquid'], true, false);

    $data['capital_pending'] = money_formating($operatons['capital_pending']);
    $data['capital_numbers_pending'] = money_formating($operatons['capital_numbers_pending'], true, false);

    echo json_encode($data);
  }

  public function appoint()
  {
    $this->load->model('ClientPack_model');
    $clientPack_model = new ClientPack_model();
    echo json_encode(array('data' => $clientPack_model->appoint()));
  }

  public function remove($_id)
  {
    $this->load->library('Utils');
    $util = new Utils();

    if (!empty($_id)) {
      $this->load->model(['ClientPeriod_model', 'ClientPack_model', 'Client_model']);
      $clientPack_model = new ClientPack_model();
      $clientPeriod_model = new ClientPeriod_model();
      $client_model = new Client_model();

      $this->_checkRemove($_id);
      if (empty($this->data['errors'])) {
        $remove = $clientPack_model->get_by_id($_id);
        $log = $util->getClientPackToLog($remove);

        $clientPeriod_model->delete_pack($_id);
        $clientPack_model->delete($_id);

        $client = $client_model->get_by_id($remove['clientpack_client']);
        $client_model->less_balance($client, $remove['clienpack_final']);

        $this->logCreate($clientPack_model, $log, 'd');
      } else {
        echo json_encode($this->data);
        die();
      }
    }
    echo json_encode([]);
  }

  private function _checkRemove($_id)
  {
    $this->load->model('ClientPeriod_model');
    $remove = $this->ClientPack_model->get_by_id($_id);
    $periods = $this->ClientPeriod_model->get_product_periods($remove['clientpack_client'], $_id);
    if (!empty($periods)) {
      foreach ($periods as $p) {
        if ($p['clientperiod_paid_date'] != NULL) {
          $this->data['errors'][] = '<p>No es posible eliminar el producto, ya existen pagos realizados</p>';
          break;
        }
      }
    }
  }

  public function getRoadmaps()
  {
    $this->load->model(['ClientPack_model']);
    $clientpack_model = new ClientPack_model();

    $get = $this->input->get();
    if ($get) {
      $road = $clientpack_model->getRoadmap($get['search']['value']);
      $data = [];
      foreach ($road as $clientpack) {
        $data[] = [
          'id' => $clientpack['clientpack_roadmap'],
          'name' => $clientpack['clientpack_roadmap']
        ];
      }
      echo json_encode($data);
    } else {
      echo [];
    }
  }

  public function getClientsRoad($road)
  {
    $this->load->model(['ClientPack_model']);
    $clientpack_model = new ClientPack_model();

    $get = $this->input->get();
    if ($get) {
      $clients = $clientpack_model->getClientsRoad($road, $get['search']['value']);
      $data = [];
      foreach ($clients as $clientpack) {
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

  public function reImport($dir = '_home_nominator_in_')
  {
    echo shell_exec("sudo /var/www/html/cobranzasnominator/importFiles.sh $dir 2>&1");
    // echo shell_exec("/media/devs/freelance/log_cobranzas/importFiles.sh $dir 2>&1");
  }

  public function export($days = 1, $manual = '0', $dir = '_home_nominator_out_')
  {
    echo shell_exec("sudo /var/www/html/cobranzasnominator/exportFiles.sh $dir $days $manual 2>&1");
    // echo shell_exec("/media/devs/freelance/log_cobranzas/exportFiles.sh $dir $days 2>&1");
  }
}
