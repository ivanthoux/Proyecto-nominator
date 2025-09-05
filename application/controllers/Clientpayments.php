<?php

class Clientpayments extends ManagerController
{

  protected $pathindex = '/clientpayments/all';
  protected $viewpath = 'manager/clients/payments/clientpayment';

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
    if (empty($client)) {
      redirect('clients');
    }
    $this->load->model('Payment_model');
    $this->load->model('Client_model');
    $viewpath = $this->viewpath . '_list';
    $this->datatables_assets();
    $this->data['client_id'] = $client;
    $this->data['filters'] = $this->Payment_model->get_filters(false);
    unset($this->data['filters']['pay_office']);
    unset($this->data['filters']['pay_officelocation']);
    $this->data['client'] = $this->Client_model->get_by_id($client);
    if (!empty($this->data['client']['client_parent'])) {
      $this->data['parent'] = $this->Client_model->get_by_id($this->data['client']['client_parent']);
    }
    $this->data['activesidebar'] = 'clients';
    $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/bootstrap-datepicker3.min.css'];
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
    $this->_render($viewpath);
  }

  public function form($client = false, $_id = false, $force_period = false, $roadmap = false)
  {
    if (in_array($this->session->userdata('user_rol'), ["seller", "sell_for"])) {
      redirect($this->pathindex, 'refresh');
    } else {
      $this->load->model(['Payment_model', 'ClientPeriod_model', 'ClientPack_model', 'Client_model', 'Pack_model', 'PackDiscounts_model', 'Banks_model']);
      $payment_model = new Payment_model();
      $client_model = new Client_model();
      $clientPack_model = new ClientPack_model();
      $clientPeriod_model = new ClientPeriod_model();
      $pack_model = new Pack_model();
      $banks_model = new Banks_model();
      $this->data['banks'] = $banks_model->get_all();
      $this->load->library('Utils');
      $utils = new Utils();

      $viewpath = $this->viewpath . '_form';

      $this->data['client_id'] = $client;
      if (!empty($client)) {
        $this->data['client'] = $client_model->get_by_id($client);
        if (!empty($this->data['client']['client_parent'])) {
          $this->data['parent'] = $client_model->get_by_id($this->data['client']['client_parent']);
        }
        $ctrl = true;
        $msg = [];
        if (empty($this->data['client']['client_email']) || !filter_var($this->data['client']['client_email'], FILTER_VALIDATE_EMAIL)) {
          $ctrl = false;
          $msg[] = 'E-mail';
        }
        if (empty($this->data['client']['client_mobile'])) {
          $ctrl = false;
          $msg[] = 'Teléfono Celular';
        }

        $this->data['client']['client_ctrl'] = $ctrl;
        $this->data['client']['client_ctrl_msg'] = '<ul><li>' . join('</li><li>', $msg) . '</li></ul>';
      }

      $post = $this->input->post();
      if (!empty($post)) {
        // $this->dd($post);
        $sign = !empty($post['sign']) ? $post['sign'] : false;
        unset($post['sign']);
        // $this->dd($post);
        // Validation first
        $clients = $post['client'];
        $isASimplifiedPayment = isset($post['isSimplePayment']) && $post['isSimplePayment'] ? true : false;
        if (!$isASimplifiedPayment){
          foreach ($clients as $client) {
            $this->_checkConstraint($client);
          }
        }
        $payments = [];
        foreach ($clients as $client) {
          if (empty($this->data['errors'])) {
            if (empty($client) && empty($roadmap)) {
              redirect($this->pathindex . '/' . $client, 'refresh');
            }
            if (empty($post['pay_id'])) { //is payment insert
              $client['pay_date'] = $post['pay_date'];
              $payment = $utils->savePayment($client, $isASimplifiedPayment); //Pasar un arreglo clientperiods
              $thereIsAPayment = !empty($payment);
              if ($thereIsAPayment) $payments[] = $payment;
              if ($thereIsAPayment && $sign) {
                $payment_sign = str_replace('data:image/png;base64,', '', $sign);
                $payment_sign = str_replace(' ', '+', $payment_sign);
                $payment_sign = base64_decode($payment_sign);
                $pay_canvas = 'signature_' . time() . '.png';
                $sign_path = './resources/payment_signatures/' . $pay_canvas;
                $img_upload = file_put_contents($sign_path, $payment_sign);
                if ($img_upload) {
                  $payment_model->save([
                    'pay_id' => $payment,
                    'pay_lat' => $post['pay_lat'],
                    'pay_lng' => $post['pay_lng'],
                    'pay_canvas' => $pay_canvas,
                  ]);
                }
              }
            }
          }
        }
        $data = join("_", $payments);
        if (empty($this->data['errors'])){
          if (!empty($data)) {
            redirect('/clientperiods/receipt/' . $data, 'refresh');
          } else {
            redirect('/clientpayments/all/' . $client, 'refresh');
          }
        }
      }

      $forced = false;
      $packDiscounts_model = new PackDiscounts_model();
      if (!empty($_id) && empty($roadmap)) {
        $this->data['edit'] = $payment_model->get_by_id($_id);
        if (!empty($this->data['edit']['client_parent'])) {
          $this->data['parent'] = $client_model->get_by_id($this->data['edit']['client_parent']);
        }
      } else if (empty($roadmap)) {
        $this->data['periods'] = $clientPeriod_model->get_client_payment($client);
        if (!empty($force_period)) {
          $forced = $clientPeriod_model->get_by_id($force_period);
          if (in_array($forced['clientpack_state'], ['2', '6'])) {
            $pack = $pack_model->get_by_id($forced['clientpack_package']);
            $forced = array_merge($forced, $pack);
          } else {
            $forced = false;
          }
        }
      } else {
        // It's from roadmap.
        $clientPeriod_model->extend_datatable_query();
        $this->data['periods'] = $this->db->get($clientPeriod_model->table())->result_array();
        $ctrl = true;
        $msg = [];
        foreach ($this->data['periods'] as $value) {
          $packs = $clientPack_model->get_where(['clientpack_id' => $value['clientperiod_pack']]);
          $this->data['clients'] = $client_model->get_by_id($packs[0]['clientpack_client']);

          if (empty($this->data['clients']['client_email']) || !filter_var($this->data['clients']['client_email'], FILTER_VALIDATE_EMAIL)) {
            $ctrl = false;
            $msg[0] = 'E-mail';
          }
          if (empty($this->data['clients']['client_mobile'])) {
            $ctrl = false;
            $msg[1] = 'Teléfono Celular';
          }
        }
        $this->data['clients']['client_ctrl'] = $ctrl;
        $this->data['clients']['client_ctrl_msg'] = '<ul><li>' . join('</li><li>', $msg) . '</li></ul>';
      }
      // $this->dd($this->data['periods']);
      if (!empty($forced)) {
        $this->data['periods'] = [$forced];
      }

      if (!empty($this->data['periods'])) {
        foreach ($this->data['periods'] as $key => $period) {
          $last_pay = $payment_model->get_by_clientperiod($period['clientperiod_id']);
          if (!empty($last_pay)) {
            $last_pay = $last_pay[0];
            if (date('U', strtotime($this->data['periods'][$key]['clientperiod_date_2'])) < date('U', strtotime($last_pay['pay_date']))) {
              $this->data['periods'][$key]['clientperiod_date_2'] = $last_pay['pay_date'];
            }
          }
          if (isset($period['clientpack_package'])) {
            $pack = $pack_model->get_by_id($period['clientpack_package']);
            $this->data['periods'][$key] = array_merge($this->data['periods'][$key], $pack);
            $discount = $packDiscounts_model->getDiscount($period['clientpack_package'], $period['clientpack_sessions'], $period['clientpack_created_at']);
            if (!empty($discount)) {
              $this->data['periods'][$key] = array_merge($this->data['periods'][$key], $discount);
            }
          } else {
            $clientpack = $this->ClientPack_model->get_by_id($period['clientperiod_pack']);
            $pack = $pack_model->get_by_id($clientpack['clientpack_package']);

            $this->data['periods'][$key] = array_merge($this->data['periods'][$key], $pack);

            $discount = $packDiscounts_model->getDiscount($clientpack['clientpack_package'], $clientpack['clientpack_sessions'], $clientpack['clientpack_created_at']);
            if (!empty($discount)) {
              $this->data['periods'][$key] = array_merge($this->data['periods'][$key], $discount);
            }
          }
        }
      }

      $clientperiodsByClient = [];
      $itsFromRoadMap = !empty($roadmap);
      if ($itsFromRoadMap) {
        foreach ($this->data['periods'] as $clientPeriod) {
          $clientId = $clientPeriod['client_id'];
          $clientperiodsByClient[$clientId][] = $clientPeriod;
        }
      } else {
        foreach ($this->data['periods'] as $clientPeriod) {
          $clientId = $client;
          $clientPeriod['client_doc'] = $this->data['client']['client_doc'];
          $clientPeriod['client_id'] = $this->data['client']['client_id'];
          $clientperiodsByClient[$clientId][] = $clientPeriod;
        }
      }
      $totalClientPeriodsAmountByClient = [];
      $totalClientPeriodsAmount = 0;
      foreach ($clientperiodsByClient as $clientId => $clientPeriods) {
        $totalClientPeriodAmountByClient = 0;
        foreach ($clientPeriods as $clientPeriod) {
          $totalClientPeriodAmountByClient += $clientPeriod['clientperiod_amount'];
        }
        $totalClientPeriodsAmountByClient[$clientId] = $totalClientPeriodAmountByClient;
        $totalClientPeriodsAmount += $totalClientPeriodAmountByClient;
      }
      $this->data['clientperiodsByClient'] = $clientperiodsByClient;
      $this->data['totalClientPeriodsAmount'] = $totalClientPeriodsAmount;
      $this->data['totalClientPeriodsAmountByClient'] = $totalClientPeriodsAmountByClient;

      $this->data['clientperiod'] = $force_period;
      $this->data['activesidebar'] = (empty($roadmap) ? 'clients' : 'periods_today');
      $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
      $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.12/handlebars.min.js'];
      $this->css[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css'];
      $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js'];
      // $this->dd($this->data);
      $this->_render($this->viewpath . '_form');
    }
  }

  public function datatables()
  {
    $this->load->model('Payment_model');
    echo json_encode($this->Payment_model->datatables_ajax_list());
  }

  public function remove($_id)
  {
    $this->load->model(['Payment_model', 'Client_model', 'ClientPeriod_model', 'ClientPack_model', 'PaymentClientperiods_model']);
    $payment_model = new Payment_model();
    $clientPeriod_model = new ClientPeriod_model();
    $client_model = new Client_model();
    $clientPack_model = new ClientPack_model();
    $paymentClientperiods_model = new PaymentClientperiods_model();

    $remove = $payment_model->get_by_id($_id);
    // $this->dd($remove);
    $paymentClientperiods = $paymentClientperiods_model->get_by_payment($_id);
    $amount = 0;
    $capital = 0;
    $interest = 0;
    $interest_2 = 0;
    if ($paymentClientperiods) {
      foreach ($paymentClientperiods as $paymentClientperiod) {
        $period = $clientPeriod_model->get_by_id($paymentClientperiod['pay_period_clientperiod']);
        $clientpack = $clientPack_model->get_by_id($period['clientperiod_pack']);
        $client = $client_model->get_by_id($clientpack['clientpack_client'], true);
        if (round($period['clientperiod_amount'], 2) == 0) {
          $amount = $paymentClientperiod['pay_period_amount'] + $paymentClientperiod['pay_period_discount'];
          $capital = $paymentClientperiod['pay_period_capital'];
          $interest = $paymentClientperiod['pay_period_interest'];
          $interest_2 = $paymentClientperiod['pay_period_interest_2'];
        } else {
          $amount = round($period['clientperiod_amount'] + $paymentClientperiod['pay_period_amount'] + $paymentClientperiod['pay_period_discount'], 2);
          $capital = round($period['clientperiod_amountcapital'] + $paymentClientperiod['pay_period_capital'], 2);
          $interest = round($period['clientperiod_amountinterest'] + $paymentClientperiod['pay_period_interest'], 2);
          $interest_2 = round($period['clientperiod_amountinterest_2'] + $paymentClientperiod['pay_period_interest_2'], 2);
        }
        $clientPeriod_model->save([
          'clientperiod_amount' => $amount,
          'clientperiod_amountcapital' => $capital,
          'clientperiod_amountinterest' => $interest,
          'clientperiod_amountinterest_2' => $interest_2,
          'clientperiod_paid' => 0,
          'clientperiod_paid_date' => NULL,
          'clientperiod_visited' => '0000-00-00 00:00:00',
          'clientperiod_id' => $period['clientperiod_id']
        ]);
        $balance_difference = $paymentClientperiod['pay_period_amount'] + $paymentClientperiod['pay_period_discount'];
        $client_model->add_balance($client, $balance_difference);
        $paymentClientperiods_model->delete($paymentClientperiod['pay_period_id']);
      }
      $payment_model->delete($_id);
      $this->logCreate($payment_model, $remove, 'd');
      echo json_encode([]);
    } else {
      $payment_model->delete($_id);
      $this->logCreate($payment_model, $remove, 'd');
      if (!empty($remove['pay_client'])) {
        $amount = $remove['pay_amount'];
        $client = $client_model->get_by_id($remove['pay_client'], true);
        $client_model->add_balance($client, $amount);
      }
      $this->logCreate($this->Payment_model, $remove, 'd');
      redirect($this->pathindex, 'refresh');
    }
  }

  private function _checkConstraint($post)
  {
    $isASimplifiedPayment = isset($post['isSimplePayment']) && $post['isSimplePayment'] ? true : false;
    if ($isASimplifiedPayment) return;
    $payments_details = isset($post['paymentDetail']) ? $post['paymentDetail'] : [];
    $totalAmountAvailableToPay = 0;
    foreach ($payments_details as $payments_detail) {
      $totalAmountAvailableToPay += $payments_detail['pay_amount'];
    }
    foreach ($post['clientperiod'] as $key => $period) {
      if (empty($period['checked'])) continue;
      $pay_daytask = round($post['clientperiod'][$key]['pay_daytask'], 2);
      // $pay_discount = round($post['clientperiod'][$key]['pay_discount'], 2);
      $period_amount = round($post['clientperiod'][$key]['period_amount'], 2);
      $pay_amount = $totalAmountAvailableToPay >= ($period_amount + $pay_daytask) ? round($period_amount + $pay_daytask, 2) : $totalAmountAvailableToPay;
      $totalAmountAvailableToPay = $totalAmountAvailableToPay - $pay_amount;
      if (is_nan($pay_amount) || $pay_amount < 0) {
        $this->data['errors'][] = '<p>El valor del <b>Pago</b> en la cuota '
          . $post['clientperiod'][$key]['packperiod'] . ' del producto '
          . $post['clientperiod'][$key]['packtitle'] . ' no es <b>v&aacute;lido</b></p>';
      }

      if ($pay_amount < $pay_daytask) {
        $this->data['errors'][] = '<p>El valor del <b>Pago</b> en la cuota '
          . $post['clientperiod'][$key]['packperiod'] . ' del producto '
          . $post['clientperiod'][$key]['packtitle'] . ' no puede ser inferior al punitorio '
          . money_formating($pay_daytask) . ' (Monto designado: '.money_formating($pay_amount).')</p>';
      }

      // OJOOOOOOOOOOOOO comparar mayor que el float arrastra decimales y no responde a la logica, DEJAR ASI!!!
      if (round(($pay_amount) - ($pay_daytask), 2) > ($period_amount)) {
        $this->data['errors'][] = '<p>El valor del <b>Pago</b> en la cuota '
          . $post['clientperiod'][$key]['packperiod'] . ' del producto '
          . $post['clientperiod'][$key]['packtitle'] . ' es superior al pago necesario</p>';
      }
    }
  }
}