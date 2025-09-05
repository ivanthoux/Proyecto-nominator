<?php

use Dompdf\Dompdf;

class Clientperiods extends ManagerController
{

  protected $pathindex = '/clientperiods/all';
  protected $viewpath = 'manager/clients/periods/clientperiod';

  public function __construct()
  {
    parent::__construct();
  }

  public function index($client = false)
  {
    redirect($this->pathindex, 'refresh');
  }

  public function all($client = false)
  {
    if (empty($client))
      redirect('clients');

    $this->load->model(['ClientPeriod_model', 'Client_model']);
    $clientPeriod_model = new ClientPeriod_model();
    $client_model = new Client_model();

    $viewpath = $this->viewpath . '_list';
    $this->data['activesidebar'] = 'clients';

    $this->datatables_assets();

    $this->data['client_id'] = $client;
    $this->data['filters'] = $clientPeriod_model->get_filters(false);
    $this->data['client'] = $client_model->get_by_id($client);
    $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/bootstrap-datepicker3.min.css'];
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
    $this->_render($viewpath);
  }

  /**
   * @deprecated
   * @param type $client
   * @param type $_id
   */
  public function form($client = false, $_id = false)
  {
    $this->load->model('ClientPeriod_model');
    $this->load->model('Client_model');
    $this->viewpath = 'manager/payments/payment';
    $viewpath = $this->viewpath . '_form';
    $this->data['client_id'] = $client;
    if (!empty($client)) {
      $this->data['client'] = $this->Client_model->get_by_id($client, true);
      if (!empty($this->data['client']['client_parent'])) {
        $this->data['parent'] = $this->Client_model->get_by_id($this->data['client']['client_parent']);
      }
    }
    $post = $this->input->post();
    if (!empty($post)) { //saving user received
      $_id = $this->ClientPeriod_model->save($post);
      $this->logCreate($this->ClientPeriod_model, $post, 'u');
      if (!empty($client)) {
        if (empty($post['pay_id'])) {
          $this->Client_model->less_balance($this->data['client'], $post['pay_amount']);
        }
        redirect('/clientperiods/all/' . $client, 'refresh');
      } else {
        redirect($this->pathindex . '/' . $client, 'refresh');
      }
    } else { //render new user form or edit user
      if (!empty($_id)) {
        $this->data['edit'] = $this->ClientPeriod_model->get_by_id($_id);
        if (!empty($this->data['edit']['client_parent'])) {
          $this->data['parent'] = $this->Client_model->get_by_id($this->data['edit']['client_parent']);
        }
      }
    }
    $this->data['activesidebar'] = 'clients';
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
    $this->_render($this->viewpath . '_form');
  }

  public function datatables()
  {
    $this->load->model('ClientPeriod_model');
    $clientPeriod_model = new ClientPeriod_model();

    $data = $clientPeriod_model->datatables_ajax_list();

    $get = $this->input->get();
    if (!empty($get['datefilter'])) {
      $dates = explode('/', $get['datefilter']);
      if (count($dates) > 1) {
        // $this->dd("este");
        $start = date('Y-m-d 00:00:00', strtotime(trim($dates[0])));
        $end = date('Y-m-d 23:59:59', strtotime(trim($dates[1])));
      } else {
        // $this->dd("no este");
        $start = false;
        $end = date('Y-m-d 23:59:59', strtotime(trim($get['datefilter'])));
      }
      $data['balances'] = $clientPeriod_model->balances($start, $end);
    }

    echo json_encode($data);
  }

  public function remove($_id)
  {
    $this->load->model('ClientPeriod_model');
    $this->load->model('Client_model');
    $remove = $this->ClientPeriod_model->get_by_id($_id);
    $this->ClientPeriod_model->delete($_id);
    if (!empty($remove['pay_client'])) {
      $client = $this->ClientPeriod_model->get_by_id($remove['pay_client'], true);
      redirect('/clientperiods/all/' . $remove['pay_client'], 'refresh');
    }
    redirect($this->pathindex, 'refresh');
  }

  public function receipt($_id = false, $detail = false, $email = false)
  {
    $this->load->model('Settings_model');
    $this->load->model('Client_model');
    $this->load->model('Pack_model');
    $this->load->model('Payment_model');
    $this->load->model('ClientPack_model');
    $this->load->model('ClientPeriod_model');
    $this->load->model('PaymentClientperiods_model');
    $this->load->library('Numbertowords', null, 'ntw');

    $paymentClientperiods_model = new PaymentClientperiods_model();
    $this->data['_id'] = $_id;
    $this->data['_detail'] = $detail ? 1 : 0;
    $files = [];
    $selectedIds = explode("_", $_id);
    foreach ($selectedIds as $key => $_id) {
      $pays = [];
      // detail: Querer ver por un comprobante, todos los pagos realizados.
      if (!$detail) {
        // $_id es de un PAYMENT
        $paymentId = $_id;
        $this->data['payments'][$key] = $this->Payment_model->get_by_id($paymentId, true);
        $paymentClientperiods = $paymentClientperiods_model->get_by_payment($paymentId);
        foreach ($paymentClientperiods as $paymentClientperiod) {
          $clientperiod_id = $paymentClientperiod['pay_period_clientperiod'];
          $clientPeriod = $this->ClientPeriod_model->get_by_id($clientperiod_id);
          $clientPeriod = array_merge($clientPeriod, $paymentClientperiod);
          $this->data['payments'][$key]['paymentClientperiods'][] = $clientPeriod;
          $this->data['edit'][] = $clientPeriod;
          $this->data['client'][$key] = $this->Client_model->get_by_id($clientPeriod['clientpack_client']);
          $this->data['clientpack'][] = $this->ClientPack_model->get_by_id($clientPeriod['clientperiod_pack']);
        }
        $pays[] = $paymentId;

        // Carga de info para correo.
        // $this->data['payments'] = [];
        // $this->data['edit'] = [];
        // $this->data['client'] = [];
        // $this->data['clientpack'] = [];
        $clientIsNotLoadedYet = !array_key_exists($this->data['client'][$key]['client_doc'], $files);
        if ($clientIsNotLoadedYet) {
          $files[$this->data['client'][$key]['client_doc']]['pays'] = [$paymentId];
          $files[$this->data['client'][$key]['client_doc']]['email'] = $this->data['client'][$key]['client_email'];
        } else {
          $files[$this->data['client'][$key]['client_doc']]['pays'][] = $paymentId;
        }
      } else {
        // $_id es de un CLIENTPERIOD
        $clientPeriodId = $_id;
        $payments = $this->Payment_model->get_by_clientperiod($clientPeriodId); //array
        $clientperiod_paid = 0;
        foreach ($payments as $payment) {
          $paymentClientperiod = $paymentClientperiods_model->get_by_clientPeriod_and_payment($clientPeriodId, $payment['pay_id']);
          $clientperiod_paid += $paymentClientperiod['pay_period_amount'];
          $clientperiod_paid += $paymentClientperiod['pay_period_discount'];
          $clientperiod_paid += $paymentClientperiod['pay_period_daytask'];
          $payment['paymentClientperiod'] = $paymentClientperiod;
          $this->data['payments'][] = $payment;
          $pays[] = $payment['pay_id'];
        }
        $clientPeriod = $this->ClientPeriod_model->get_by_id($clientPeriodId);
        $clientPeriod['paid'] = $clientperiod_paid;
        $this->data['edit'][] = $clientPeriod;

        $this->data['detail'] = true;

        $this->data['client'][] = $this->Client_model->get_by_id($this->data['edit'][0]['clientpack_client']);
        $this->data['clientpack'][] = $this->ClientPack_model->get_by_id($this->data['edit'][0]['clientperiod_pack']);
      }
    }
    $thereIsOnlyOneId = count($selectedIds) === 1;
    if ($thereIsOnlyOneId) {
      $files[$this->data['client'][0]['client_doc']]['pays'] = $pays;
      $files[$this->data['client'][0]['client_doc']]['email'] = $this->data['client'][0]['client_email'];
    }
    // $this->dd($this->data);

    $this->data['Numbertowords'] = $this->ntw;
    $this->data['setting'] = $this->session->userdata('settings');

    $this->data['pdf'] = $email;
    if ($email) {
      $this->load->library('pdf');

      // $this->dd($files);
      $send = false;
      foreach ($files as $client => $value) {
        $this->data['payments'] = [];
        $this->data['edit'] = [];
        $this->data['client'] = [];
        $this->data['clientpack'] = [];

        foreach ($value['pays'] as $key => $pay) {
          $this->data['payments'][$key] = $this->Payment_model->get_by_id($pay, true);
          $paymentClientperiods = $paymentClientperiods_model->get_by_payment($this->data['payments'][$key]['pay_id']);  //TODO: Arreglar
          foreach ($paymentClientperiods as $paymentClientperiod) {
            $clientperiod_id = $paymentClientperiod['pay_period_clientperiod'];
            $clientPeriod = $this->ClientPeriod_model->get_by_id($clientperiod_id);
            $clientPeriod = array_merge($clientPeriod, $paymentClientperiod);
            $this->data['payments'][$key]['paymentClientperiods'][] = $clientPeriod;
            $this->data['edit'][] = $clientPeriod;
            $this->data['client'][$key] = $this->Client_model->get_by_id($clientPeriod['clientpack_client']);
            $this->data['clientpack'][$key] = $this->ClientPack_model->get_by_id($clientPeriod['clientperiod_pack']);

            $clientperiod_id = $paymentClientperiod['pay_period_clientperiod'];
            // $this->data['edit'][$key][] = $this->ClientPeriod_model->get_by_id($clientperiod_id); //OJO: Se agrega un nuevo subindice a este campo. Revisar generación de pdf
          }
          //$this->data['edit'][$key] = $this->ClientPeriod_model->get_by_id($this->data['payment'][$key]['pay_clientperiod']); //Previous
        }

        if ($value['email'] !== '') {
          $dompdf = new Dompdf();
          $html = $this->load->view($this->viewpath . '_receipt', $this->data, true);
          $html = '<head><style>' . file_get_contents('assets/manager/css/styles.css') . "</style></head><body>$html</body>";
          $dompdf->loadHtml($html);
          $dompdf->render();
          $filename = $client . '.pdf';
          // $filepath = "resources/clients/" . $filename; //sys_get_temp_dir() . "/" . $filename;
          $filepath = sys_get_temp_dir() . "/" . $filename;
          fwrite(fopen($filepath, 'w'), $dompdf->output());

          $config = [
            // 'to' => 'silveiradeandrade.carlos@gmail.com',
            'to' => $value['email'],
            'subject' => 'Recibo de pago nominator',
            'message' => 'Enviamos comprobante pago',
            'files' => [
              $filename => $filepath
            ]
          ];

          send_email($config);
          $send = true;
        }
      }

      echo $send ? '{}' : '';
    } else {
      $this->data['activesidebar'] = 'clients';
      $this->javascript[] = $this->load->view('manager/clients/periods/clientperiod_receipt_js', $this->data, true);
      $this->_render($this->viewpath . '_receipt');
    }
  }

  public function getbanks($to = false, $road = false, $client = false)
  {
    $this->load->model(['ClientPeriod_model', 'Client_model']);
    $clientPeriod_model = new ClientPeriod_model();

    $viewpath = 'manager/banks/banks_list';

    $this->data['activesidebar'] = 'banks';
    $this->data['getpaid'] = true;

    $post = $this->input->post();
    if (!empty($post)) {
      $this->data['edit'] = $post;
      // $this->dd($post, true);
      $upload = array_shift($_FILES);
      // $this->dd($upload);

      $this->load->helper('upload_helper');
      $upload = upload_worker($upload, sys_get_temp_dir() . '/', 'xls', true, ['xls', 'xlsx']);
      if ($upload['error'] === 0) {
        $this->data['updload'] = $upload;
        // $this->dd($upload);

        $this->load->library(['excel']);

        $filename =  sys_get_temp_dir() . '/' . $upload['filename'];
        $excelReader = PHPExcel_IOFactory::createReaderForFile($filename);
        $excelObj = $excelReader->load($filename);
        $worksheet = $excelObj->getSheet(0); //
        $lastRow = $worksheet->getHighestRow();

        $row = 4;
        // $keys = [
        //   strtolower(str_replace(" ", "_", $worksheet->getCell('A' . $row)->getValue())),
        //   strtolower(str_replace(" ", "_", $worksheet->getCell('B' . $row)->getValue()))
        // ];
        $row++;
        for ($row; $row <= $lastRow; $row++) {
          if ($worksheet->getCell('E' . $row)->getValue())
            $data[] = [
              // $keys[0] => $worksheet->getCell('A' . $row)->getValue(),
              // $keys[1] => $worksheet->getCell('B' . $row)->getValue()
              0 => PHPExcel_Style_NumberFormat::toFormattedString($worksheet->getCell('A' . $row)->getValue(), 'DD-MM-YYYY'),
              1 => $row,
              2 => $worksheet->getCell('C' . $row)->getValue(),
              3 => $worksheet->getCell('E' . $row)->getValue()
            ];
        }

        $this->data['data'] =  $data;
      } else {
        $this->data['errors'] = $upload['error'];
      }
    }

    $this->css[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css'];
    $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js'];
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);

    $this->datatables_assets();
    $this->_render($viewpath);
  }

  public function getpaid($to = false, $road = false, $client = false)
  {
    $this->load->model(['ClientPeriod_model', 'Client_model']);
    $clientPeriod_model = new ClientPeriod_model();

    $viewpath = 'manager/periods/period_list';

    $start = false;
    if (empty($to)) {
      $end = date('Y-m-d 23:59:59');
    } else {
      $end = date('Y-m-d 23:59:59', strtotime(trim($to)));
    }
    $this->data['start'] = $start;
    $this->data['end'] = $end;
    $this->data['road'] = $road;
    $this->data['client'] = urldecode($client);
    $this->data['filters'] = $clientPeriod_model->get_filters(false);
    $this->data['balances'] = $clientPeriod_model->balances($start, $end);
    $this->data['activesidebar'] = 'periods_today';
    $this->data['getpaid'] = true;

    $this->css[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css'];
    $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js'];
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);

    $this->datatables_assets();
    $this->_render($viewpath);
  }

  public function fixall()
  {
    for ($p = 2434; $p < 2495; $p++) {
      $this->fix($p);
    }
  }

  public function fix($p = 2436)
  {
    $this->load->model('ClientPack_model');
    $this->load->model('ClientPeriod_model');

    // $p = 2434; //mensual
    // $p = 2446; //semanal

    $pack = $this->ClientPack_model->get_by_id($p, true);

    $periods = $this->ClientPeriod_model->get_product_periods(false, $p, true);

    // if($pack['clientpack_type'] === 'Mensual'){
    echo '<pre>';
    print_r($pack);

    $newdate = $pack['clientpack_created_at'];
    if ($pack['clientpack_type'] === 'Mensual') {
      if (date('d', strtotime($newdate)) >= 20) {
        $newdate = date('Y-m-10 G:00:00', strtotime($newdate));
        $newdate = date('Y-m-d G:00:00', strtotime($newdate . ' ' . '+2 month'));
      } else {
        $newdate = date('Y-m-10 G:00:00', strtotime($newdate));
        $newdate = date('Y-m-d G:00:00', strtotime($newdate . ' ' . '+1 month'));
      }

      $pack['clientpack_start'] = $newdate;
      $id = $this->ClientPack_model->save_only($pack);
    }
    if ($pack['clientpack_type'] === 'Semanal') {
      $newdate = date('Y-m-d G:00:00', strtotime($newdate));
      $pack['clientpack_start'] = $newdate;
      $id = $this->ClientPack_model->save_only($pack);
    }
    print_r($newdate);
    // print_r($id);
    // print_r($periods);

    $daystart = strtotime($newdate);
    foreach ($periods as $period) {
      $period['clientperiod_date'] = date('Y-m-d G:i', $daystart);
      // print_r($period);
      $this->ClientPeriod_model->save($period);

      if ($pack['clientpack_type'] === 'Semanal') {
        $daystart = strtotime('1 week', $daystart);
      }
      if ($pack['clientpack_type'] === 'Mensual') {
        $daystart = strtotime('next month', $daystart);
      }
      //verifica si es domingo
      if (date('w', $daystart) == 0) {
        $daystart = strtotime('next day', $daystart);
      }
    }
    // }
  }

  public function coupon()
  {
    $get = $this->input->get();
    $client = isset($get['client']) ? $get['client'] : false;

    $clientpack = isset($get['clientpack']) ? $get['clientpack'] : false;
    $date = (isset($get['datefilter']) ? $get['datefilter'] : date('Y-m-d')) . ' 00:00:00';
    // $this->dd($date);

    $this->load->model([
      'Client_model',
      'ClientPeriod_model',
      'ClientPack_model',
      'pack_model',
      'Payment_model'
    ]);
    $clientpack_model = new ClientPack_model();
    $payment_model = new Payment_model();
    $pack_model = new Pack_model();
    $clientperiod_model = new ClientPeriod_model();

    $clientperiod_model->extend_datatable_query();
    $rows = $this->db->get($clientperiod_model->table())->result_array();
    // $this->dd($rows);
    $this->data['clients'] = [];
    foreach ($rows as $row) {
      $periods[] = $row;
    }
    foreach ($rows as $row) {
      // $this->dd($row);
      // $periods = $clientperiod_model->get_client_unpaid(false, $row['clientpack_id'], false, $date);

      // foreach ($periods as $key => $period) {
      $amount = round($row['clientperiod_amount'], 2);
      $punitorios = 0;

      $pay = $payment_model->get_by_clientperiod($row['clientperiod_id']);
      if (!empty($pay)) {
        $pay = $pay[0];
        if (date('U', strtotime($row['clientperiod_date'])) < date('U', strtotime($pay['pay_date']))) {
          $period['clientperiod_date'] = $pay['pay_date'];
        }
        if (date('U', strtotime($row['clientperiod_date_2'])) < date('U', strtotime($pay['pay_date']))) {
          $period['clientperiod_date'] = $pay['pay_date'];
        } else {
          $amount = round($amount + $row['clientperiod_amountinterest_2'], 2);
          $period['clientperiod_date'] = $row['clientperiod_date_2'];
        }
      }

      $clientpack = $clientpack_model->get_by_id($row['clientpack_id']);

      // $clientpack['seller'] = $seller;
      // $last_pay = $payment_model->getLastPay($clientpack['clientpack_id']);
      // $clientpack['last_pay'] = ($last_pay ? $last_pay['pay_date'] : null);

      $pack = $pack_model->get_by_id($clientpack['clientpack_package']);

      $dt = new DateTime($row['clientperiod_date']);
      $interval = $dt->diff(new DateTime($date));
      $days = (int) $interval->format("%r%a");
      if ($days > 0) {
        $row['clientperiod_date'] = $date;
        $punitorios = round(($days * ($pack['pack_daytask'] / 100)) * $amount, 2);
      }

      $found = false;
      $idx = null;
      foreach ($this->data['clients'] as $key => $client) {
        if ($client['client_id'] == $row['client_id']) {
          $found = true;
          $idx = $key;
          break;
        }
      }
      if (!$found) {
        $this->data['clients'][] = array_merge($row, [
          'packs' => [array_merge($pack, $clientpack, [
            'periods' => [[
              'date' => $row['clientperiod_date'],
              'session' => $row['clientperiod_packperiod'],
              'days' => $days,
              'amountcapital' => round($row['clientperiod_amountcapital'], 2),
              'amountcapitalfull' => round($row['clientperiod_amountcapitalfull'], 2),
              'amount' => $amount,
              'punitorios' => $punitorios
            ]]
          ])]
        ]);
      } else {
        $found = false;
        $idxP = null;
        foreach ($this->data['clients'][$idx]['packs'] as $key => $p) {
          if ($p['clientpack_id'] == $row['clientpack_id']) {
            $found = true;
            $idxP = $key;
          }
        }
        if (!$found) {
          $this->data['clients'][$idx]['packs'][] = array_merge($pack, $clientpack, [
            'periods' => [[
              'date' => $row['clientperiod_date'],
              'session' => $row['clientperiod_packperiod'],
              'days' => $row,
              'amountcapital' => round($row['clientperiod_amountcapital'], 2),
              'amountcapitalfull' => round($row['clientperiod_amountcapitalfull'], 2),
              'amount' => $amount,
              'punitorios' => $punitorios
            ]]
          ]);
        } else {
          $this->data['clients'][$idx]['packs'][$idxP]['periods'][] = [
            'date' => $row['clientperiod_date'],
            'session' => $row['clientperiod_packperiod'],
            'days' => $days,
            'amountcapital' => round($row['clientperiod_amountcapital'], 2),
            'amountcapitalfull' => round($row['clientperiod_amountcapitalfull'], 2),
            'amount' => $amount,
            'punitorios' => $punitorios
          ];
        }
      }
      // }
      // }
    }
    // $this->dd($this->data);
    $this->data['setting'] = $this->session->userdata('settings');

    $this->data['activesidebar'] = 'periods_today';
    $this->_render($this->viewpath . '_coupon');
  }
}
