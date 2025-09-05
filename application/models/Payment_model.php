<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payment_model extends Base_model
{

  protected $table = 'payments';
  protected $single_for_view = 'pagos';
  protected $primary_key = 'pay_id';
  protected $timestamp = false;
  protected $column_order = ['pay_date', 'pay_created_at', 'pay_client', 'pay_amount', 'pay_type', 'user_firstname'];
  protected $column_search = ['pay_id', 'client_doc', 'client_firstname', 'client_lastname'];

  public function __construct()
  {
    parent::__construct();
    $this->datatable_customs_link = function ($row) {
      return $this->customLinks($row, $this->session->userdata('user_rol_label'));
    };
  }

  private function customLinks($row, $role)
  {
    $editLink = '&nbsp;';
    $removeLink = '&nbsp;';
    $recipe = '';
    $warning_text = $row['pay_period_count'] > 1 ? '<p>Este comprobante esta en un pago que implica a más de un comprobante. Si elimina el pago de este comprobante, también eliminará todos los comprobantes asociados a dicho pago. ¿Desea continuar?</p>' : '';
    if ($role == 'Super') {
      if (empty($row['officeclosing']) && empty($row['client_id'])) {
        $link = site_url('payments/form/' . $row[$this->primary_key]);
      } else if (empty($row['client_id'])) {
        $link = site_url('officeclosings/form/' . $row['officeclosing'] . '/' . $row[$this->primary_key]);
        $editLink = '<a href="' . $link . '" class="btn btn-warning " title="Editar"><span class="fa fa-edit"></span></a>';
      }

      if (empty($row['officeclosing']) && empty($row['client_id'])) {
        $link = "app.deleteConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','payments/remove/" . $row[$this->primary_key] . "','$warning_text')";
      } else if (!empty($row['client_id'])) {
        $link = "app.deleteConfirmAjax(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','" . site_url("clientpayments/remove/" . $row[$this->primary_key]) . "', [], '" . $_SERVER['HTTP_REFERER'] . "','$warning_text')";
      } else {
        $link = "app.deleteConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','officeclosings/remove/" . $row[$this->primary_key] . "','$warning_text')";
      }
      if (!empty($link)) {
        $removeLink = '<a class="btn btn-danger" onclick="' . $link . '" title="Eliminar"><span class="fa fa-remove"></span></a>';
      }
    }
    //TODO: Change this.
    if (!empty($row['pay_clientperiod'])) {
      $recipe = '<a href="' . site_url('clientperiods/receipt/' . $row['pay_id']) . '" class="btn bg-olive" title="Ver comprobante"><span class="fa fa-book"></span></a>';
    }

    return $recipe . $editLink . $removeLink;
  }

  public function save($data)
  {
    unset($data['clientperiod']);
    if (!empty($data[$this->primary_key])) {
      return $this->update($data);
    } else {
      if (isset($data['pay_date'])) {
        $data['pay_date'] = date('Y-m-d', strtotime($data['pay_date']));
      }
      $data['pay_created_by'] = $this->session->userdata('user_id');
      $data['pay_created_at'] = date('Y-m-d G:i:s');
      return $this->insert($data);
    }
  }

  public function clientpack_sessions_attribute($value, $row)
  {
    return $row['clientperiod_packperiod'] . '/' . $value;
  }

  public function pay_amount_attribute($value, $row)
  {
    return money_formating($value + $row['pay_daytask']);
  }

  public function pay_created_at_attribute($value, $row)
  {
    return date('d/m/Y', strtotime($value));
  }

  public function pay_date_attribute($value, $row)
  {
    return date('d/m/Y', strtotime($value));
  }

  public function user_firstname_attribute($value, $row)
  {
    return $row['user_firstname'] . ' ' . $row['user_lastname'];
  }

  public function pay_client_attribute($value, $row)
  {
    if (!empty($row['pay_client'])) {
      $client = $this->db->where('client_id', $row['pay_client'])->get('clients')->row_array();
    }
    return !empty($row['client_id']) ? ($row['client_firstname'] . ' ' . $row['client_lastname']) : ($client['client_firstname'] . ' ' . $client['client_lastname']);
  }

  public function get_filters($client = true)
  {
    $filters = array('pay_created_by');
    if ($client == false) {
      $filters[] = 'client_id';
    }
    $options = array();
    foreach ($filters as $field) {
      if ($field == 'pay_type') {
        $this->db->select($field)->group_by($field)->order_by($field, 'ASC');
        $temp = $this->db->get($this->table)->result_array();
        foreach ($temp as $op) {
          if (!empty($op[$field])) {
            $options[$field][] = array('title' => $op[$field], 'value' => $op[$field]);
          }
        }
      } else if ($field == 'client_id' && $client) {
        //$this->db->join('client_periods', 'clientperiod_id = pay_clientperiod', 'left')
        $this->db->select('client_id, c.client_firstname, c.client_lastname, pay_detail_type')->group_by($field)->order_by('c.client_firstname', 'ASC');
        $this->db->join('payment_clientperiods', 'pay_period_payment = pay_id', 'left')
          ->join('payment_detail', 'pay_id = pay_detail_payment', 'left')
          ->join('client_periods', 'clientperiod_id = pay_period_clientperiod', 'left')
          ->join('client_packs', 'clientpack_id = clientperiod_pack', 'left')
          ->join('clients c', 'client_id = clientpack_client', 'left');

        $temp = $this->db->get($this->table)->result_array();
        foreach ($temp as $op) {
          if (!empty($op[$field])) {
            $options[$field][] = array('title' => $op['client_firstname'] . ' ' . $op['client_lastname'], 'value' => $op[$field]);
          }
        }
      } else if ($field == 'pay_created_by') {
        $this->db->select('pay_created_by, user_firstname, user_lastname')
          ->join('users as u', 'user_id = pay_created_by')
          ->where('user_email <> ', 'SYSTEM')
          ->group_by('pay_created_by')
          ->order_by('user_firstname', 'ASC');
        $temp = $this->db->get($this->table)->result_array();
        foreach ($temp as $op) {
          if (!empty($op[$field])) {
            $options[$field][] = array('title' => $op['user_firstname'] . ' ' . $op['user_lastname'], 'value' => $op[$field]);
          }
        }
      }
    }
    // echo '<pre>';
    // print_r($options);
    // die();
    $options['pay_presented'][] = array('title' => 'Cerrado', 'value' => 1);
    $options['pay_presented'][] = array('title' => 'No Cerrado', 'value' => 0);
    return $options;
  }

  function extend_datatable_query()
  {
    $get = $this->input->get();
    $this->db->select('payments.pay_id, payments.pay_client, payments.pay_voucher, payments.pay_date, payments.pay_created_at, payments.pay_created_by, payments.pay_description, payments.pay_import, payments.pay_canvas, payments.pay_lat, payments.pay_lng, payments.pay_presented, payments.pay_presented_at')
      ->select('client_packs.*, client_periods.*, u.user_firstname, u.user_lastname, c.client_id, c.client_firstname, c.client_lastname, c.client_active');
    $this->db->select('(SELECT group_concat(DISTINCT(pay_detail_type))) as pay_type');
    $this->db->select('(SELECT SUM(payment_clientperiods.pay_period_amount) FROM payment_clientperiods WHERE payment_clientperiods.pay_period_payment = pay_id) AS pay_amount');
    $this->db->select('(SELECT SUM(payment_clientperiods.pay_period_capital) FROM payment_clientperiods WHERE payment_clientperiods.pay_period_payment = pay_id) AS pay_capital');
    $this->db->select('(SELECT SUM(payment_clientperiods.pay_period_interest) FROM payment_clientperiods WHERE payment_clientperiods.pay_period_payment = pay_id) AS pay_interest');
    $this->db->select('(SELECT SUM(payment_clientperiods.pay_period_interest_2) FROM payment_clientperiods WHERE payment_clientperiods.pay_period_payment = pay_id) AS pay_interest_2');
    $this->db->select('(SELECT SUM(payment_clientperiods.pay_period_discount) FROM payment_clientperiods WHERE payment_clientperiods.pay_period_payment = pay_id) AS pay_discount');
    $this->db->select('(SELECT SUM(payment_clientperiods.pay_period_daytask) FROM payment_clientperiods WHERE payment_clientperiods.pay_period_payment = pay_id) AS pay_daytask');
    $this->db->select('(SELECT COUNT(payment_clientperiods.pay_period_id) FROM payment_clientperiods WHERE payment_clientperiods.pay_period_payment = pay_id) AS pay_period_count');
    if (!empty($get['client'])) {
      $this->column_order = ['pay_id', 'pay_date', 'pay_amount', 'pay_type', 'user_firstname'];
      $this->db->where('clientpack_client', $get['client']);
    } else {
      if (!in_array($this->session->userdata('user_rol_label'), ['Super', 'Administrador', 'Propietario'])) {
        $this->db->where('pay_created_by', $this->session->userdata('user_id'));
        $this->db->where('pay_presented', 0);
      }
    }

    if (!empty($get['filter'])) {
      foreach ($get['filter'] as $filter => $val) {
        if ($filter == "pay_presented") {
          if (trim($val) == 0) {
            $this->db->where($filter, '0');
          } else {
            $this->db->where($filter . ' > ', '0');
          }
        } elseif (!empty($val)) {
          $this->db->where($filter, trim($val));
        }
      }
    }

    if (!empty($get['datefilter'])) {
      $dates = explode('/', $get['datefilter']);
      $start = date('Y-m-d 00:00:00', strtotime(trim($dates[0])));
      $end = date('Y-m-d 23:59:00', strtotime(trim($dates[1])));
      $this->db->where('pay_date >= "' . $start . '" AND pay_date <= "' . $end . '"');
    }
    $this->db->join('users as u', 'user_id = pay_created_by');
    //$this->db->join('client_periods', 'clientperiod_id = pay_clientperiod', 'left')
    $this->db->join('payment_clientperiods', 'pay_period_payment = pay_id', 'left')
      ->join('payment_detail', "pay_id = pay_detail_payment AND pay_detail_type <> 'Cuenta Corriente'", 'left')
      ->join('client_periods', 'clientperiod_id = pay_period_clientperiod', 'left')
      ->join('client_packs', 'clientpack_id = clientperiod_pack', 'left')
      ->join('clients c', 'client_id = clientpack_client', 'left')
      ->group_by('pay_id');
    //$this->dd($this->db->get_compiled_select());
    //$this->dd($this->db->get_compiled_select($this->table));
  }

  public function get_by_clientperiod($clientperiod_id)
  {
    //  return $this->db->select('pay_clientperiod, pay_id, pay_date, pay_discount, pay_daytask, pay_amount, pay_lat, pay_lng')
    //  ->where('pay_clientperiod', $clientperiod_id)
    return $this->db->select('pay_period_clientperiod as pay_clientperiod, pay_id, pay_date, pay_period_discount as pay_discount, pay_period_daytask as pay_daytask, pay_period_amount as pay_amount, pay_lat, pay_lng')
      ->join('payment_clientperiods', 'pay_period_payment = pay_id', 'left')
      ->join('payment_detail', 'pay_id = pay_detail_payment', 'left')
      ->where('pay_period_clientperiod', $clientperiod_id)
      ->where('pay_detail_type <> ', 'Cuenta Corriente')
      ->order_by('pay_date', 'desc')
      ->group_by('pay_id')
      ->get($this->table)->result_array();
  }

  public function getLastPay($clientpack_id)
  {
    //    return $this->db->select('pay_clientperiod, pay_id, pay_date, pay_discount, pay_daytask, pay_amount')
    //      ->join('client_periods', 'pay_clientperiod = clientperiod_id')
    return $this->db->select('payment_clientperiods.pay_period_clientperiod, pay_id, pay_date, payment_clientperiods.pay_period_discount, payment_clientperiods.pay_period_daytask, payment_clientperiods.pay_period_amount')
      ->join('payment_clientperiods', 'pay_period_payment = pay_id', 'left')
      ->join('client_periods', 'pay_period_clientperiod = clientperiod_id')
      ->where('clientperiod_pack', $clientpack_id)
      ->order_by('pay_date', 'desc')
      ->limit(1)
      ->get($this->table)->row_array();
  }

  public function getWithOutVoucher()
  {
    $settings = $this->session->userdata('settings');
    $days = empty($days) ? 7 : $settings['voucher'];
    //  ->where('pay_clientperiod is not null', null, false)
    //  ->where('pay_interest >', 0)
    //OJO: Agregar interest_2??
    return $this->db->where('pay_voucher', null)
      ->join('payment_clientperiods', 'pay_period_payment = pay_id', 'left')
      ->where('pay_period_clientperiod is not null', null, false)
      ->where('pay_period_interest >', 0)
      ->where('pay_date >=', '2020-01-01')
      ->where('pay_date <', date('Y-m-d', strtotime(('-' . $days . ' days'))))
      ->order_by('pay_date')
      // ->limit(10)
      ->get($this->table)->result_array();
    // ->get_compiled_select($this->table);
  }

  public function getCtasCobradas($start, $end, $pack = false, $ctacte = false, $system = false)
  {
    $this->db->select('*')

      // ->select('coalesce((select office_name from offices where office_id = clientpack_office), "") clientpack_officename')
      // ->select('coalesce((select officelocation_name from office_locations where officelocation_id = clientpack_officelocation), "") clientpack_officelocationname')

      // ->select('coalesce((select office_name from offices where office_id = pay_office), "") pay_officename')
      // ->select('coalesce((select officelocation_name from office_locations where officelocation_id = pay_officelocation), "") pay_officelocationname')

      // ->join('client_periods', 'pay_clientperiod = clientperiod_id')
      ->select('client_lastname, client_firstname, client_doc')
      ->select('pay_amount')
      ->select('SUM(pay_period_capital) AS pay_period_capital_total')
      ->select('SUM(pay_period_amount) AS pay_period_amount_total')
      //->select("REPLACE(JSON_EXTRACT(pay_detail_extra_data, '$.pay_bank_cod'), '".'"'."', ".'""'.") AS pay_detail_bank_cod")
      //->select("REPLACE(JSON_EXTRACT(pay_detail_extra_data, '$.pay_expiration_date'), '".'"'."', ".'""'.") AS pay_detail_expiration_date")
      //->select("REPLACE(JSON_EXTRACT(pay_detail_extra_data, '$.pay_clearing'), '".'"'."', ".'""'.") AS pay_detail_clearing")
      //->select("REPLACE(JSON_EXTRACT(pay_detail_extra_data, '$.pay_number'), '".'"'."', ".'""'.") AS pay_detail_number")
      //->select("REPLACE(JSON_EXTRACT(pay_detail_extra_data, '$.pay_cuit'), '".'"'."', ".'""'.") AS pay_detail_cuit")
      ->select('COUNT(pay_period_id) AS pay_period_count')
      ->select('COUNT(DISTINCT(clientpack_id)) AS pay_clientpack_count')
      //->select('COUNT(pay_detail_id) AS pay_detail_count')
      //->join('payment_detail', 'pay_id = pay_detail_payment')
      ->join('payment_clientperiods', 'pay_period_payment = pay_id')
      ->join('client_periods', 'pay_period_clientperiod = clientperiod_id')
      ->join('client_packs', 'clientpack_id = clientperiod_pack')
      ->join('clients', 'clientpack_client = client_id')
      ->join('users', 'pay_created_by = user_id ')
      ->where("pay_date between '" . $start . "' AND '" . $end . "'")
      ->group_by("pay_id")
      ->order_by("pay_date");

    if (!empty($ctacte)) {
      $this->db->where('pay_detail_type', 'Cuenta Corriente');
    }
    if (!empty($system)) {
      $this->db->where('user_email <> ', 'SYSTEM');
    }
    if (!empty($pack)) {
      $this->db->where('clientpack_package', $pack);
    }

    // $this->dd($this->db->get_compiled_select($this->table), true);
    // $this->dd($this->db->get_compiled_select($this->table));
    return $this->db->get($this->table)->result_array();
    // $this->dd($data, true);
    // return $data;
  }

  public function getChecksSummary($filter_data)
  {
    $this->load->model('Banks_model');
    $banks_model = new Banks_model();
    $banks = $banks_model->get_all();
    // $this->db->select("REPLACE(JSON_EXTRACT(pay_detail_extra_data, '$.pay_bank_cod'), '".'"'."', ".'""'.") AS pay_bank_cod, SUM(pay_detail_amount) AS pay_amount_by_bank, COUNT(pay_detail_id) AS pay_count_bank")
    $this->db->select("pay_detail_extra_data, pay_detail_amount")
      ->where('pay_detail_type LIKE "%Cheque%"')
      ->join('payment_detail', 'pay_id = pay_detail_payment', 'left');
    // ->group_by('pay_bank_cod');

    if (!empty($filter_data['pay_presented'])) {
      $this->db->where('pay_presented', $filter_data['pay_presented']);
    }
    if (!empty($filter_data['start']) && !empty($filter_data['end'])) {
      $this->db->where('pay_date >= "' . $filter_data['start'] . '" AND pay_date <= "' . $filter_data['end'] . '"');
    }
    if (!in_array($this->session->userdata('user_rol_label'), ['Super', 'Administrador', 'Propietario'])) {
      $this->db->where('pay_created_by', $this->session->userdata('user_id'));
    }

    $payments = $this->db->get($this->table);

    if (!$payments) {
      return [];
    }
    $payments = $payments->result_array();
    $groupedPaymentsByBank = [];
    foreach ($payments as $payment) {
      $payExtraData = $payment['pay_detail_extra_data'];
      $payExtraDataJson = json_decode($payExtraData);
      $bankCod = $payExtraDataJson->pay_bank_cod;
      if (empty($groupedPaymentsByBank[$bankCod])) {
        $groupedPaymentsByBank[$bankCod] = [
          "pay_bank_cod" => $bankCod,
          "pay_amount_by_bank" => 0,
          "pay_count_bank" => 0
        ];
      }
      $groupedPaymentsByBank[$bankCod]['pay_amount_by_bank'] += $payment['pay_detail_amount'];
      $groupedPaymentsByBank[$bankCod]['pay_count_bank']++;
    }
    $payments = array_values($groupedPaymentsByBank);
    $payments = array_map(function ($payment) use ($banks) {
      $bank_code = $payment['pay_bank_cod'];
      $bank = array_filter($banks, function ($bank) use ($bank_code) {
        return $bank['bank_cod'] == $bank_code;
      });
      $bank = array_pop($bank);
      $payment['pay_bank_name'] = $bank['bank_name'];
      return $payment;
    }, $payments);
    return $payments;
  }

  public function get_by_id($_id, $detailed = false)
  {
    $this->load->model(['PaymentClientperiods_model', 'Banks_model', 'PaymentDetail_model']);
    $paymentClientperiods_model = new PaymentClientperiods_model();
    $paymentDetail_model = new PaymentDetail_model();
    $payment = parent::get_by_id($_id);
    $payment['pay_discount'] = 0;
    $payment['pay_daytask'] = 0;
    $payment['pay_interest'] = 0;
    $payment['pay_interest_2'] = 0;
    $payment['pay_capital'] = 0;
    $paymentClientperiods = $paymentClientperiods_model->get_by_payment($_id);
    foreach ($paymentClientperiods as $paymentClientperiod) {
      $payment['pay_discount'] += $paymentClientperiod['pay_period_discount'];
      $payment['pay_daytask'] += $paymentClientperiod['pay_period_daytask'];
      $payment['pay_interest'] += $paymentClientperiod['pay_period_interest'];
      $payment['pay_interest_2'] += $paymentClientperiod['pay_period_interest'];
      $payment['pay_capital'] += $paymentClientperiod['pay_period_capital'];
    }

    if ($detailed) {
      $payment['details'] = [];
      $payment_details = $paymentDetail_model->get_by_payment($_id);
      foreach ($payment_details as $payment_detail) {
        if ($payment_detail['pay_detail_type'] == 'Cheque') {
          $banks_model = new Banks_model();
          $banks = $banks_model->get_all();
          $extraData = json_decode($payment_detail['pay_detail_extra_data'], true);
          $bank_code = $extraData['pay_bank_cod'];
          $bank = array_filter($banks, function ($bank) use ($bank_code) {
            return $bank['bank_cod'] == $bank_code;
          });
          $bank = array_pop($bank);
          $extraData['pay_bank_name'] = $bank['bank_name'];
          $payment_detail['pay_detail_extra_data'] = json_encode($extraData);
        }
        $payment['details'][] = $payment_detail;
      }
    }
    return $payment;
  }

  public function delete($paymentId)
  {
    $this->load->model(['PaymentDetail_model', 'PaymentClientperiods_model', 'ClientPeriod_model', 'Client_model', 'AdvancedPayment_model']);
    $paymentDetail_model = new PaymentDetail_model();
    $paymentClientperiods_model = new PaymentClientperiods_model();
    $clientPeriod_model = new ClientPeriod_model();
    $client_model = new Client_model();
    $advancedPayment_model = new AdvancedPayment_model();
    $payment_client_period_details = $paymentClientperiods_model->get_by_payment($paymentId);

    if (!empty($payment_client_period_details)) {
      foreach ($payment_client_period_details as $payment_client_period_detail) {
        $clientPeriodId = $payment_client_period_detail['pay_period_clientperiod'];

        $clientPeriodToUpdate = $clientPeriod_model->get_by_id($clientPeriodId);
        $totalAmountToRestore = round($payment_client_period_detail['pay_period_amount'], 2);
        $newClientPeriodTotalAmount = round($clientPeriodToUpdate['clientperiod_amount'] + $totalAmountToRestore, 2);
        $capitalAmountToRestore = $payment_client_period_detail['pay_period_capital'];
        $newClientPeriodCapitalAmount = round($clientPeriodToUpdate['clientperiod_amountcapital'] + $capitalAmountToRestore, 2);
        $interestAmountToRestore = $payment_client_period_detail['pay_period_interest'];
        $newClientPeriodInterestAmount = round($clientPeriodToUpdate['clientperiod_amountinterest'] + $interestAmountToRestore, 2);
        $interest2AmountToRestore = $payment_client_period_detail['pay_period_interest_2'];
        $newClientPeriodInterest2Amount = round($clientPeriodToUpdate['clientperiod_amountinterest_2'] + $interest2AmountToRestore, 2);

        $clientToUpdateBalance = $client_model->get_by_client_period($clientPeriodId);
        $client_model->add_balance($clientToUpdateBalance, $totalAmountToRestore);

        // In order to make idempotent elimination. If the deletion fails, at least we can rest assure that all the ramifications are don and the remainings are just payments with zero amount and no impact 
        $paymentClientperiods_model->save([
          'pay_period_id' => $payment_client_period_detail['pay_period_id'],
          'pay_period_amount' => 0,
          'pay_period_capital' => 0,
          'pay_period_interest' => 0,
          'pay_period_interest_2' => 0,
        ]);
        $clientPeriodToSave = [
          'clientperiod_id' => $clientPeriodId,
          'clientperiod_amount' => $newClientPeriodTotalAmount,
          'clientperiod_amountcapital' => $newClientPeriodCapitalAmount,
          'clientperiod_amountinterest' => $newClientPeriodInterestAmount,
          'clientperiod_amountinterest_2' => $newClientPeriodInterest2Amount,
          'clientperiod_paid_date' => 'NULL'
        ];
        $shoudSetClientPeriodAsUnpaid = $totalAmountToRestore > 0;
        if ($shoudSetClientPeriodAsUnpaid)
          $clientPeriodToSave['clientperiod_paid'] = 0;

        $clientPeriod_model->save($clientPeriodToSave);
        $paymentClientperiods_model->delete($payment_client_period_detail['pay_period_id']);
      }
    }
    $payment_details = $paymentDetail_model->get_by_payment($paymentId);
    $thereArePaymentDetails = !empty($payment_details);
    if ($thereArePaymentDetails) {
      foreach ($payment_details as $payment_detail) {
        $paymentDetail_model->delete($payment_detail['pay_detail_id']);
      }
    }
    $advancedPayments = $advancedPayment_model->get_by_payment($paymentId);
    $thereIsAnAdvancedPayment = !empty($advancedPayments);
    if ($thereIsAnAdvancedPayment){
      $advancedPayment_model->save([
        'advanced_pay_id' => $advancedPayments['advanced_pay_id'],
        'advanced_pay_amount' => 0
      ]);
      $advancedPayment_model->delete($advancedPayments['advanced_pay_id']);
    }
    
    parent::delete($paymentId);
  }
}
