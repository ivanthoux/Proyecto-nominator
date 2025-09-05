<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Utils
 *
 * @author harlyman
 */
class Utils
{
  private $state = 2;
  private $controller;

  public function __construct()
  {
    $this->CI = &get_instance();
  }

  public function dd($object)
  {
    echo '<pre>' . print_r($object, true) . '</pre>';
    die();
  }

  public function setController($controller)
  {
    $this->controller = $controller;
  }

  public function savePayment($data, $isASimplifiedPayment = false)
  {
    $this->CI->load->model(['ClientPeriod_model', 'Payment_model', 'Client_model', 'ClientPack_model', 'PaymentClientperiods_model', 'PaymentDetail_model', 'AdvancedPayment_model']);
    $clientPeriod_model = new ClientPeriod_model();
    $payment_model = new Payment_model();
    $client_model = new Client_model();
    $clientPack_model = new ClientPack_model();
    $paymentClientperiods_model = new PaymentClientperiods_model();
    $paymentDetail_model = new PaymentDetail_model();
    $advancedPayment_model = new AdvancedPayment_model();

    $clientPeriods = isset($data['clientperiod']) ? $data['clientperiod'] : [];
    $payments_details = isset($data['paymentDetail']) ? $data['paymentDetail'] : [];
    // $this->dd($data);
    $payDate = $data['pay_date'];

    if ($isASimplifiedPayment) {
      // If it's a simplified payment then all the clientperiods are fully paid with a payment consisting only of cash.
      // Achieve this by overwriting payment details with one payment detail equal to the sum of the implied clientperiods of cash type.
      $total_pay_amount = 0;
      foreach ($clientPeriods as $clientPeriod_id => $clientPeriod_form) {
        if (!isset($clientPeriod_form['checked'])) continue;
        $clientPeriod = $clientPeriod_model->get_by_id($clientPeriod_id, true);

        $clientPeriodFirstExpiration = $clientPeriod['clientperiod_date'];
        $dt = new DateTime($payDate);
        $interval = $dt->diff(new DateTime($clientPeriodFirstExpiration));
        $days = (int) $interval->format("%r%a");
        $mustPayInterest2 = $days > 0;

        $period_amount      = round($clientPeriod['clientperiod_amount'], 2);
        $period_task = 0;
        if (isset($clientPeriod_form['pay_daytask']))
          $period_task      = round($clientPeriod_form['pay_daytask'], 2);
        if (isset($clientPeriod_form['task']))
          $period_task      = round($clientPeriod_form['task'], 2);
        $period_amount      += $period_task;
        $period_capital     = round($clientPeriod['clientperiod_amountcapital'], 2);
        $period_interest    = round($clientPeriod['clientperiod_amountinterest'], 2);
        $period_interest_2  = $mustPayInterest2 ? round($clientPeriod['clientperiod_amountinterest_2'], 2) : 0;

        $total_pay_amount += ($period_capital + $period_interest + $period_interest_2 + $period_task);
      }
      if ($total_pay_amount <= 0) return null;
      $payments_details = [];
      $payments_details[] = [
        'pay_amount' => $total_pay_amount,
        'pay_type' => 'Efectivo',
      ];
    }
    $total_pay_amount = 0;
    foreach ($payments_details as $payment_detail) {
      $total_pay_amount = $total_pay_amount + $payment_detail['pay_amount'];
    }
    $pay_id = null;

    $itsPositivePayment = $total_pay_amount > 0;
    if (!$itsPositivePayment) return null;
    //Create a payment header
    foreach ($clientPeriods as $clientPeriodId => $clientPeriod) {
      $clientpack = $clientPack_model->get_by_clientperiod_id($clientPeriodId);
      $client = $client_model->get_by_id($clientpack['clientpack_client'], true);
      break;
    }

    $payment_header = array();
    $payment_header['pay_date'] = $data['pay_date'];
    $payment_header['pay_description'] = $data['pay_description'];
    $payment_header['pay_client'] = $client['client_id'];
    $payment_header['pay_amount'] = $total_pay_amount;

    $paymentHasVoucher = !empty($data['pay_voucher']);
    if ($paymentHasVoucher) {
      $payment_header['pay_voucher'] = $data['pay_voucher'];
    }
    $paymentHasImport = !empty($data['import']);
    if ($paymentHasImport) {
      $payment_header['pay_import'] = $data['pay_import'];
    }

    $pay_id = $payment_model->save($payment_header);

    //process all the payment_detail

    foreach ($payments_details as $detail) {
      $pay_det = array();
      $pay_det['pay_detail_payment'] = $pay_id;
      $pay_det['pay_detail_amount'] = $detail['pay_amount'];
      $pay_det['pay_detail_type'] = $detail['pay_type'];
      $pay_det['pay_detail_extra_data'] = $this->payment_extra_data($detail);

      $paymentDetail_model->save($pay_det);
    }
    $remaining_amount = round($total_pay_amount, 2);
    //process all the clientperiods
    foreach ($clientPeriods as $clientPeriod_id => $clientPeriod_form) {
      if (!isset($clientPeriod_form['checked'])) {
        continue;
      }
      unset($clientPeriod_form['checked']);
      $clientPeriod = $clientPeriod_model->get_by_id($clientPeriod_id, true);

      $clientPeriodFirstExpiration = $clientPeriod['clientperiod_date'];

      $dt = new DateTime($payDate);
      $interval = $dt->diff(new DateTime($clientPeriodFirstExpiration));
      $days = (int) $interval->format("%r%a");

      $mustPayInterest2 = $days > 0;

      $period_amount      = round($clientPeriod_form['period_amount'], 2);
      $period_task        = round((isset($clientPeriod_form['pay_daytask']) ? $clientPeriod_form['pay_daytask'] : 0), 2);
      $period_amount      += $period_task;
      $period_capital     = round($clientPeriod['clientperiod_amountcapital'], 2);
      $period_interest    = round($clientPeriod['clientperiod_amountinterest'], 2);
      $period_interest_2  = $mustPayInterest2 ? round($clientPeriod['clientperiod_amountinterest_2'], 2) : 0;

      $canPayEntirely = round($remaining_amount, 2) >= round($period_capital + $period_interest + $period_interest_2 + $period_task, 2);
      if ($canPayEntirely) {
        $payment_clientperiod = array();
        $payment_clientperiod['pay_period_payment'] = $pay_id;
        $payment_clientperiod['pay_period_clientperiod'] = $clientPeriod_id;
        $payment_clientperiod['pay_period_amount'] = $period_amount;
        $payment_clientperiod['pay_period_capital'] = $period_capital;
        $payment_clientperiod['pay_period_interest'] = $period_interest;
        $payment_clientperiod['pay_period_interest_2'] = $period_interest_2;
        $payment_clientperiod['pay_period_discount'] = 0;
        $payment_clientperiod['pay_period_daytask'] = $period_task;

        $paymentClientperiods_model->save($payment_clientperiod);

        $remaining_amount = round($remaining_amount - $payment_clientperiod['pay_period_daytask'], 2);
        $remaining_amount = round($remaining_amount - $payment_clientperiod['pay_period_interest_2'], 2);
        $remaining_amount = round($remaining_amount - $payment_clientperiod['pay_period_interest'], 2);
        $remaining_amount = round($remaining_amount - $payment_clientperiod['pay_period_capital'], 2);

        $clientPeriod['clientperiod_amount'] = 0;
        $clientPeriod['clientperiod_amountcapital'] = 0;
        $clientPeriod['clientperiod_amountinterest'] = 0;
        $clientPeriod['clientperiod_amountinterest_2'] = 0;
        $clientPeriod['clientperiod_paid'] = 1;
        $clientPeriod['clientperiod_paid_date'] = $data['pay_date'];
        //update clientPeriod
        $clientPeriod_model->save($clientPeriod);
        //less balance for the client
        $client_model->less_balance($client, round($payment_clientperiod['pay_period_amount'], 2));
      } else {
        $doesntHavePeriodTask = $period_task == 0;
        if ($doesntHavePeriodTask) {
          $payment_clientperiod = array();
          $payment_clientperiod['pay_period_payment'] = $pay_id;
          $payment_clientperiod['pay_period_clientperiod'] = $clientPeriod_id;
          $payment_clientperiod['pay_period_discount'] = 0;
          $payment_clientperiod['pay_period_daytask'] = 0;

          $payment_clientperiod['pay_period_interest_2'] = ($remaining_amount >= $period_interest_2) ? $period_interest_2 : $remaining_amount;
          $remaining_amount = round($remaining_amount - $payment_clientperiod['pay_period_interest_2'], 2);
          $payment_clientperiod['pay_period_interest'] = ($remaining_amount >= $period_interest) ? $period_interest : $remaining_amount;
          $remaining_amount = round($remaining_amount - $payment_clientperiod['pay_period_interest'], 2);
          $payment_clientperiod['pay_period_capital'] = ($remaining_amount >= $period_capital) ? $period_capital : $remaining_amount;
          $remaining_amount = round($remaining_amount - $payment_clientperiod['pay_period_capital'], 2);
          $payment_clientperiod['pay_period_amount'] = round($payment_clientperiod['pay_period_interest_2'] + $payment_clientperiod['pay_period_interest'] + $payment_clientperiod['pay_period_capital'], 2);

          $paymentClientperiods_model->save($payment_clientperiod);

          $clientPeriod['clientperiod_amount'] = round($period_amount - $payment_clientperiod['pay_period_amount'], 2);
          $clientPeriod['clientperiod_amountcapital'] = round($period_capital - $payment_clientperiod['pay_period_capital'], 2);
          $clientPeriod['clientperiod_amountinterest'] = round($period_interest - $payment_clientperiod['pay_period_interest'], 2);
          $clientPeriod['clientperiod_amountinterest_2'] = round($period_interest_2 - $payment_clientperiod['pay_period_interest_2'], 2);
          //update clientPeriod
          $clientPeriod_model->save($clientPeriod);
          //less balance for the client
          $client_model->less_balance($client, round($payment_clientperiod['pay_period_amount'], 2));
        } else {
          $itsEnoughToFullfillDayTask = $remaining_amount >= $period_task;
          if ($itsEnoughToFullfillDayTask) {
            $payment_clientperiod = array();
            $payment_clientperiod['pay_period_payment'] = $pay_id;
            $payment_clientperiod['pay_period_clientperiod'] = $clientPeriod_id;
            $payment_clientperiod['pay_period_discount'] = 0;

            $payment_clientperiod['pay_period_daytask'] = $period_task;
            $remaining_amount = round($remaining_amount - $payment_clientperiod['pay_period_daytask'], 2);
            $payment_clientperiod['pay_period_interest_2'] = ($remaining_amount >= $period_interest_2) ? $period_interest_2 : $remaining_amount;
            $remaining_amount = round($remaining_amount - $payment_clientperiod['pay_period_interest_2'], 2);
            $payment_clientperiod['pay_period_interest'] = ($remaining_amount >= $period_interest) ? $period_interest : $remaining_amount;
            $remaining_amount = round($remaining_amount - $payment_clientperiod['pay_period_interest'], 2);
            $payment_clientperiod['pay_period_capital'] = ($remaining_amount >= $period_capital) ? $period_capital : $remaining_amount;
            $remaining_amount = round($remaining_amount - $payment_clientperiod['pay_period_capital'], 2);
            $payment_clientperiod['pay_period_amount'] = $payment_clientperiod['pay_period_daytask'] + $payment_clientperiod['pay_period_interest_2'] + $payment_clientperiod['pay_period_interest'] + $payment_clientperiod['pay_period_capital'];

            $paymentClientperiods_model->save($payment_clientperiod);

            $clientPeriod['clientperiod_amount']            = round($period_amount - $payment_clientperiod['pay_period_amount'], 2);
            $clientPeriod['clientperiod_amountcapital']     = round($period_capital - $payment_clientperiod['pay_period_capital'], 2);
            $clientPeriod['clientperiod_amountinterest']    = round($period_interest - $payment_clientperiod['pay_period_interest'], 2);
            $clientPeriod['clientperiod_amountinterest_2']  = round($period_interest_2 - $payment_clientperiod['pay_period_interest_2'], 2);
            //update clientPeriod
            $clientPeriod_model->save($clientPeriod);
            //less balance for the client
            $client_model->less_balance($client, round($payment_clientperiod['pay_period_amount'], 2));
          } else {
            $payment_clientperiod = array();
            $payment_clientperiod['pay_period_payment']       = $pay_id;
            $payment_clientperiod['pay_period_amount']        = 0;
            $payment_clientperiod['pay_period_clientperiod']  = $clientPeriod_id;
            $payment_clientperiod['pay_period_discount']      = 0;
            $payment_clientperiod['pay_period_daytask']       = 0;
            $payment_clientperiod['pay_period_interest_2']    = 0;
            $payment_clientperiod['pay_period_interest']      = 0;
            $payment_clientperiod['pay_period_capital']       = 0;

            $paymentClientperiods_model->save($payment_clientperiod);
          }
        }
      }
    }
    if ($remaining_amount >= 1) {
      //Create "Documento agregado de suma cero"
      $advanced_payment = array();
      $advanced_payment['advanced_pay_payment'] = $pay_id;
      $advanced_payment['advanced_pay_amount'] = $remaining_amount;
      $advancedPayment_model->save($advanced_payment);
    }
    return $pay_id;
  }

  public function importPay(&$data, $packs, $periods, $client)
  {
    $this->CI->load->model(['ClientPack_model', 'Pack_model', 'Payment_model', 'Client_model', 'ClientPeriod_model', 'PaymentDetail_model']);
    $clientpack_model = new ClientPack_model();
    $pack_model = new Pack_model();
    $payment_model = new Payment_model();
    $client_model = new Client_model();
    $clientPeriod_model = new ClientPeriod_model();
    $paymentDetail_model = new PaymentDetail_model();
    $itsCreditNote = isset($data['pay_type']) && ($data['pay_type'] === 'Nota de Credito');

    $totalPaymentAmount = 0;
    foreach ($periods as $key => $period) {
      if (!$packs) {
        $clientpack = $clientpack_model->get_by_id($period['clientperiod_pack']);
        $pack = $pack_model->get_by_id($clientpack['clientpack_package']);
      } else
        $pack = $packs;

      // echo $data['cve'] . '-' . $data['comprobante'] . '-' . $data['importe_comprobante'] . '<br>';
      // $this->dd($period);
      $dt = new DateTime($data['emision']);
      $interval = $dt->diff(new DateTime($period['clientperiod_date']));
      $days = (int) $interval->format("%r%a");

      $dt = new DateTime($data['emision']);
      $interval = $dt->diff(new DateTime($period['clientperiod_date_2']));
      $days = (int) $interval->format("%r%a");
      $taskVal = 0;
      if ($days > 0) {
        $task = round(($pack['pack_daytask'] * $days) / 100, 2);
        $taskVal = round(($period['clientperiod_amount'] * $task), 2);
        $pay_amount = round(($period['clientperiod_amount'] + $taskVal), 2);
      } else {
        $pay_amount = round($period['clientperiod_amount'], 2);
      }

      $importe_comprobante = round(abs($data['importe_cuota']) > $pay_amount ? $pay_amount : abs($data['importe_cuota']), 2);
      $data['importe_cuota'] = round($data['importe_cuota'] + $importe_comprobante, 2);

      $periods[$key]["task"] = $taskVal;
      $periods[$key]["totalAmountToPay"] = $importe_comprobante;
      $totalPaymentAmount += $importe_comprobante;

      if ($data['importe_cuota'] >= 0) {
        break;
      }
    }

    $emulated_post = array();
    $emulated_post['pay_date'] = $data['emision'];
    $emulated_post['pay_voucher'] = $data['pay_voucher'];
    $emulated_post['pay_description'] = (isset($data['pay_description']) ? $data['pay_description'] : null);
    $emulated_post['pay_import'] = (isset($data['pay_import']) ? $data['pay_import'] : null);

    $emulated_post['clientperiod'] = array();
    foreach ($periods as $period) {
      $hasTaskEvaluated = isset($period['task']);
      if (!$hasTaskEvaluated) break;
      $emulated_post['clientperiod'][$period['clientperiod_id']] = array();
      $emulated_post['clientperiod'][$period['clientperiod_id']]['checked'] = 1;
      $emulated_post['clientperiod'][$period['clientperiod_id']]['task'] = $period['task'];
      $emulated_post['clientperiod'][$period['clientperiod_id']]['period_amount'] = round($period['clientperiod_amount'], 2);
    }

    $emulated_post['paymentDetail'] = array();
    $emulated_post['paymentDetail'][0] = array();
    $emulated_post['paymentDetail'][0]['pay_type'] = $data['pay_type'] ? $data['pay_type'] : "Efectivo";
    $emulated_post['paymentDetail'][0]['pay_amount'] = $totalPaymentAmount;

    $this->savePayment($emulated_post);


    $thereIsRemainingCashAvailable = $data['importe_cuota'] < 0;
    if ($thereIsRemainingCashAvailable && !$packs) {
      // echo $data['cve'] . '-' . $data['comprobante'] . '-' . $data['importe_comprobante'] . '<br>';
      //anticipo
      //echo "Estoy por procesar un importe negativo, probablemente de un anticipo";
      $payment_header = array();
      $payment_header['pay_date'] = $data['emision'];
      $payment_header['pay_description'] = (isset($data['pay_description']) ? $data['pay_description'] : null);
      $payment_header['pay_client'] = $client['client_id'];
      $payment_header['pay_voucher'] = (isset($data['pay_voucher']) ? $data['pay_voucher'] : null);
      $payment_header['pay_amount'] = abs($data['importe_cuota']);
      $payment_header['pay_import'] = (isset($data['pay_import']) ? $data['pay_import'] : null);
      $payment_id = $payment_model->save($payment_header);
      //echo "payment_id = $payment_id";

      $payment_detail = array();
      $payment_detail['pay_detail_payment'] = $payment_id;
      $payment_detail['pay_detail_type'] = (isset($data['pay_type']) ? $data['pay_type'] : 'Adelanto');
      $payment_detail['pay_detail_amount'] = abs($data['importe_cuota']);
      $paymentDetail_model->save($payment_detail);

      $client_model->less_balance($client, round($payment_header['pay_amount'], 2));
    }
  }

  public function postForm($post, $redirect, $controller, $balance = true)
  {
    $this->controller = $controller;

    // $this->dd($post);
    $pack_model = new Pack_model();
    $client_model = new Client_model();
    $clientPack_model = new ClientPack_model();
    $clientPeriod_model = new ClientPeriod_model();

    $this->controller->data['client'] = $client_model->get_by_id($post['clientpack_client']);
    $this->controller->data['client_active_periods'] = $clientPeriod_model->get_client_unpaid_by_product($post['clientpack_client']);

    $client = $post['clientpack_client'];
    $rules = array();
    $rulesPost = array();
    foreach ($post as $key => $value) {
      if (strpos($key, 'packrule') !== false) {
        $rule = explode('_', $key);
        $rules[] = [
          'packrule_id' => $rule[1],
          'packrule_value' => $value
        ];
        $rulesPost[$key] = $value;
      }
    }
    $this->_checkRules($rules, $post); // check rules and set de state un $this->state
    $post['clientpack_state'] = (!isset($post['clientpack_state']) ? $this->state : ($post['clientpack_state'] == 2 && $this->state == 5 ? 6 : $post['clientpack_state']));
    $audited = $post['clientpack_state'] != $this->state;
    if (empty($this->controller->data['errors'])) {
      $pack = $pack_model->get_by_id($post['clientpack_package']);

      // set fixed parameters
      $post['clientpack_title'] = (!empty($post['clientpack_title']) ? $post['clientpack_title'] : $pack['pack_name'] . " (" . $post['clientpack_sessions'] . ")");
      $post['clientpack_type'] = $pack['pack_type'];
      $post['clientpack_commision'] = $pack['pack_commision'];
      $post['clientpack_expenses'] = $pack['pack_expenses'];
      $post['clientpack_final'] = round($post['clientpack_sessions_price'], 2) * round($post['clientpack_sessions'], 2);

      $this->_checkConstraint($post);
      // $this->dd($this->controller->data['errors']);
      if (empty($this->controller->data['errors']) && empty($post['clientpack_verify'])) {
        unset($post['clientpack_verify']);
        if (!empty($post['clientpack_package'])) {
          if ($audited) {
            $post['clientpack_audited'] = date('Y-m-d H:i:00');
          }

          if (empty($post['clientpack_id']) && in_array($post['clientpack_state'], [2, 6])) {
            $post['clientpack_audited'] = date('Y-m-d H:i:00');
            $post['clientpack_viewed'] = date('Y-m-d H:i:00');
          }

          if ($redirect) {
            $post['periods'] = true;
          }
          $id = $clientPack_model->save($post, $this->controller->data['client']);
          unset($this->controller->data['client']['clientbalance']);
          if ($balance)
            $client_model->add_balance($this->controller->data['client'], $post['clientpack_final']);

          if ($redirect) {
            unset($post['periods']);
          }

          if (empty($post['clientpack_id'])) {
            $post = $clientPack_model->get_by_id($id);

            if ($redirect) $this->_sendEmailByState($post, false);

            $post = $this->getClientPackToLog($post);

            $this->_logCreate($clientPack_model, $post, 'c');

            if ($redirect) $this->_sendEmailContract($this->controller->data['client'], $id);
            if ($redirect) $this->_sendEmailReceipt($this->controller->data['client'], $id);

            if ($redirect) redirect('/clientpacks/all/' . $client, 'refresh');
          } else {
            $post = $this->getClientPackToLog($post);

            $this->_logCreate($clientPack_model, $post, 'u');
            $post = $clientPack_model->get_by_id($id);
          }
          if ($redirect) $this->_sendEmailByState($post, $audited);
          if ($post['clientpack_state'] == 2) {
            // $this->dd([$post, $this->state]);
            if ($redirect) redirect('/clientpayments/form/' . $client, 'refresh');
          }
          // $this->dd([$post, $this->state]);
          if ($redirect) redirect('/clientpacks/all/' . $client, 'refresh');
        }
        if ($redirect) redirect('/clientpacks/all/' . $client, 'refresh');
      } else {
        $post['packrules'] = $rulesPost;
        $this->controller->data['edit'] = $post;
      }
    } else {
      $this->controller->data['edit'] = $post;
      $this->controller->data['edit']['packrules'] = $rulesPost;
      $old = $clientPack_model->get_by_id($post['clientpack_package']);
      $this->controller->data['edit']['user_firstname'] = $old['user_firstname'];
      $this->controller->data['edit']['user_lastname'] = $old['user_lastname'];
      // $this->dd($this->controller->data['edit']);
    }
    return $this->controller->data;
  }

  private function _checkRules($packrules, $post)
  {
    if (isset($post['clientpack_state'])) {
      $this->state = $post['clientpack_state'];
    } else {
      $this->_checkDocuments($packrules, $post['clientpack_client']);
    }
  }

  private function _checkConstraint($post)
  {
    if (empty($post['clientpack_id'])) {
      $pack_model = new Pack_model();
      $client_model = new Client_model();

      $pack = $pack_model->get_by_id($post['clientpack_package']);
      $client = $client_model->get_by_id($post['clientpack_client']);

      if (!$client['client_mobile_validate'] && $pack['pack_phone_validate'] == 1) {
        $this->controller->data['errors'][] = '<p>Debe validar el <b>número de Celular</b> del cliente</p>';
      }
      if (!$client['client_ref1_phone_validate'] && $pack['pack_phone_ref_validate'] == 1) {
        $this->controller->data['errors'][] = '<p>Debe validar el <b>número de Celular</b> del Referido 1</p>';
      }

      if (is_nan(floatval($post['clientpack_price'])) || floatval($post['clientpack_price']) <= 0) {
        $this->controller->data['errors'][] = '<p>El valor del <b>Precio</b> no es <b>v&aacute;lido</b></p>';
      }
      if (is_nan(floatval($post['clientpack_expenses'])) || floatval($post['clientpack_expenses']) < 0) {
        $this->controller->data['errors'][] = '<p>El valor del <b>Gasto Administrativo</b> no es <b>v&aacute;lido</b></p>';
      }
    }
    $this->_checkSeesions($post);
    $this->_checkPrice($post);
  }

  private function _sendEmailByState($clientPack, $audited)
  {
    $send = true;

    $emails = '';
    $subject = '';
    switch ($clientPack['clientpack_state']) {
      case '1':
      case '5':
        $subject = "Crédito Pendiente";
        if (!$audited) { //pending create
          $emails = $this->User_model->get_emails_by_role('super');
          // $emails = implode("; ", $emails);

          $data['user'] = $this->User_model->get_by_id($clientPack['clientpack_created_by']);
          $data['office'] = $this->Office_model->get_by_id($data['user']['user_office']);
        } else { // pendig edit
          $send = false;
        }
        break;
      case '2':
      case '6':
        $subject = "Crédito Autorizado" . ($clientPack['clientpack_state'] == 6 ? " A Documentar" : "");
        if ($audited) {
          $user = $this->User_model->get_by_id($clientPack['clientpack_created_by']);
          $emails = $user['user_email'];

          $data['user'] = $this->User_model->get_by_id($audited);
        } else {
          $send = false;
        }
        break;
      case '3':
        $subject = "Crédito Rechazado";
        if ($audited) {
          $user = $this->User_model->get_by_id($clientPack['clientpack_created_by']);
          $emails = $user['user_email'];

          $data['user'] = $this->User_model->get_by_id($audited);
        } else {
          $send = false;
        }
        break;
    }
    $data['clientpack'] = $clientPack;
    $data['client'] = $this->CI->Client_model->get_by_id($clientPack['clientpack_client']);
    $data['pack'] = $this->CI->Pack_model->get_by_id($clientPack['clientpack_package']);
    $body = $this->CI->load->view('mails/packs', $data, true);

    if ($send) { // && $_SERVER['CI_ENV'] === "production") {
      send_email([
        'to' => $emails,
        'subject' => $subject,
        'message' => $body,
      ]);
    }
  }

  private function _sendEmailContract($client, $id)
  {
    if (!empty($client['client_email'])) {
      $this->controller->dataContract($client['client_id'], $id);

      $subject = 'Contrato de Financiamiento - nominator';
      $body = $this->CI->load->view('manager/clients/packs/clientpack_contract', array_merge($this->controller->data, ['email' => true]), true);

      send_email([
        'to' => $client['client_email'],
        'subject' => $subject,
        'message' => $body,
      ]);
    }
  }

  private function _sendEmailReceipt($client, $id)
  {
    if (!empty($client['client_email'])) {
      $this->controller->dataReceipt($client['client_id'], $id, 'periods');

      $subject = 'Detalle de cuotas - nominator';
      $body = $this->CI->load->view('manager/clients/packs/clientpack_receipt', array_merge($this->controller->data, ['email' => true]), true);

      send_email([
        'to' => $client['client_email'],
        'subject' => $subject,
        'message' => $body,
      ]);
    }
  }

  private function _checkDocuments($packrules, $clientpack_client)
  {
    $this->CI->load->model('Rules_model');
    $clientFile_model = new ClientFile_model();
    $packRules_model = new PackRules_model();
    $rules_model = new Rules_model();

    $files = $clientFile_model->get_files_by_client($clientpack_client);

    $types = [];
    if (!empty($files)) {
      foreach ($files as $file) {
        $types[] = $file['clientfile_type'];
      }
    }

    $er = [];
    foreach ($packrules as $packrule) {
      $pr = $packRules_model->get_by_id($packrule['packrule_id']);
      $r = $rules_model->get_by_id($pr['packrule_rule'], ['rule_active', true]);
      if (($r['rule_type'] == 1 && $pr['packrule_value'] > $packrule['packrule_value'])
        || ($r['rule_type'] == 2 && $pr['packrule_value'] < $packrule['packrule_value'])
        || ($r['rule_type'] == 3 && $pr['packrule_value'] != $packrule['packrule_value'])
      ) {
        $this->controller->data['errors'][] = '<p>La regla <i><b>' . $r['rule_name'] . '</i> NO</b> cumple la condici&oacute;n</p>';
      }
      if (!in_array($r['rule_id'], $types) && $r['rule_type_doc_require'] && $this->state <= 2) {
        $this->state += 4;
      }
    }
    if (count($er) > 0) {
      $this->controller->data['errors'][] = '<p>Al cliente le falta cargar los archivos de: ' . join(', ', $er) . '</p>';
    }
  }

  private function _checkSeesions($clientPack)
  {
    if (empty($clientPack['clientpack_id'])) {
      $pack_model = new Pack_model();

      $pack = $pack_model->get_by_id($clientPack['clientpack_package']);
      if (floatval($clientPack['clientpack_sessions']) < floatval($pack['pack_session_min']) || floatval($clientPack['clientpack_sessions']) > floatval($pack['pack_session_max'])) {
        $this->controller->data['errors'][] = '<p>El n&uacute;mero de cuotas <b>NO</b> puede ser menor a ' . $pack['pack_session_min'] . ' ni superior a ' . $pack['pack_session_max'] . '</p>';
      }
    }
  }

  private function _checkPrice($clientPack)
  {
    if (empty($clientPack['clientpack_id'])) {
      $pack_model = new Pack_model();

      $pack = $pack_model->get_by_id($clientPack['clientpack_package']);

      if (floatval($clientPack['clientpack_price']) > floatval($pack['pack_price'])) {
        $this->controller->data['errors'][] = '<p>El precio <b>NO</b> puede superar $ ' . number_format($pack['pack_price'], 2, ',', '.') . '</p>';
      }

      $old_packs_unpaid_periods = $this->controller->data['client_active_periods'];
      $old_packs_unpaid_periods_sum = 0;
      if (!empty($old_packs_unpaid_periods)) {
        foreach ($old_packs_unpaid_periods as $unpaid) {
          $old_packs_unpaid_periods_sum += $unpaid['clientpack_sessions_price'];
        }
      }
    }
  }

  public function dataContract($client, $_id)
  {
    $this->CI->load->model(['Settings_model', 'Pack_model', 'ClientPack_model', 'ClientPeriod_model', 'Client_model']);
    $this->CI->load->library('Numbertowords', null, 'ntw');
    $client_model = new Client_model();
    $clientPack_model = new ClientPack_model();
    $pack_model = new Pack_model();
    $clientPeriod_model = new ClientPeriod_model();

    $this->controller->data['client_id'] = $client;
    $this->controller->data['Numbertowords'] = $this->ntw;
    $this->controller->data['client'] = $client_model->get_by_id($client);
    $this->controller->data['edit'] = $clientPack_model->get_by_id($_id);
    $this->controller->data['pack'] = $pack_model->get_by_id($this->controller->data['edit']['clientpack_package']);
    $this->controller->data['setting'] = $this->session->userdata('settings');

    $this->controller->data['period'] = $clientPeriod_model->get_product_periods($client, $_id)[0];
  }

  public function dataReceipt($client, $_id, $periods)
  {
    $this->CI->load->model(['Settings_model', 'Pack_model', 'PackDiscounts_model', 'ClientPack_model', 'ClientPeriod_model', 'Client_model']);
    $this->CI->load->library('Numbertowords', null, 'ntw');
    $client_model = new Client_model();
    $clientPack_model = new ClientPack_model();
    $clientPeriod_model = new ClientPeriod_model();
    $pack_model = new Pack_model();
    $packDiscounts_model = new PackDiscounts_model();

    $this->controller->data['client_id'] = $client;
    $this->controller->data['Numbertowords'] = $this->CI->ntw;
    $this->controller->data['client'] = $client_model->get_by_id($client);
    $this->controller->data['edit'] = $clientPack_model->get_by_id($_id);
    $this->controller->data['pack'] = $pack_model->get_by_id($this->controller->data['edit']['clientpack_package']);
    $discounts = $packDiscounts_model->getDiscount($this->controller->data['edit']['clientpack_package'], $this->controller->data['edit']['clientpack_sessions'], $this->controller->data['edit']['clientpack_created_at']);
    if ($discounts) {
      $this->controller->data['pack'] = array_merge($this->controller->data['pack'], $discounts);
    }
    $this->controller->data['setting'] = $this->CI->session->userdata('settings');
    if (!empty($periods)) {
      $this->controller->data['periods'] = $clientPeriod_model->get_product_periods($client, $_id);
    } else {
      $this->controller->data['period'] = $clientPeriod_model->get_product_periods($client, $_id)[0];
    }
  }

  public function getClientPackToLog($post)
  {
    $toRemove = [];
    foreach ($post as $key => $value) {
      if (strpos($key, 'clientpack_') === false) {
        $toRemove[] = $key;
      }
    }
    foreach ($toRemove as $value) {
      unset($post[$value]);
    }
    $post['clientpack_rules'] = $this->CI->ClientPack_model->get_rules($post['clientpack_id']);
    return $post;
  }

  private function _logCreate($model, $object, $acction)
  {
    $this->CI->load->model('UserActions_model');
    $data['useraction_user'] = $this->CI->session->userdata('user_id');
    $data['useraction_date'] = date('Y-m-d G:i:s');
    $data['useraction_action'] = $acction;
    $data['useraction_detail'] = json_encode($object);
    $data['useraction_entity'] = $model->table();
    $data['useraction_entity_id'] = $object[$model->primary_key()];
    if (!isset($object[$model->primary_key()]) || $object[$model->primary_key()] === null || $object[$model->primary_key()] === '') {
      die('El id del objeto no puede ser vacio');
    }
    $this->CI->UserActions_model->save($data);
  }

  private function payment_extra_data($extra_data)
  {
    $return = [];
    $payment_extra_data = ['pay_bank_cod', 'pay_expiration_date', 'pay_clearing', 'pay_number', 'pay_cuit', 'pay_transaction_number'];
    foreach ($payment_extra_data as $key) {
      $data = (isset($extra_data[$key]) ? $extra_data[$key] : '');
      if (!empty($data)) {
        $return[$key] = $data;
      }
      unset($extra_data[$key]);
    }
    return json_encode($return);
  }

  public function createImportNotification($notificationId, $importId, $notificationObservation, $controller)
  {
    $this->CI->load->model(['Notifications_model']);
    $notifications_model = new Notifications_model();
    $notificationId = $notifications_model->addNewDetail($notificationId, $importId, $notificationObservation);
    $controller->data['notifications'][$notificationId]['details'][] = $notificationObservation;
    return $notificationId;
  }
}
