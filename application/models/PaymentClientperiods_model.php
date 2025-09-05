<?php

defined('BASEPATH') or exit('No direct script access allowed');

class PaymentClientperiods_model extends Base_model
{

  protected $table = 'payment_clientperiods';
  protected $single_for_view = 'payment_clientperiods';
  protected $primary_key = 'pay_period_id';
  protected $timestamp = false;
  protected $column_order = ['pay_period_amount', 'pay_period_capital', 'pay_period_interest', 'pay_period_interest_2'];
  protected $column_search = [];

  public function __construct()
  {
    parent::__construct();

    $this->datatable_customs_link = function ($row) {
      $buttons = '';
      return $buttons;
    };
  }

  public function save($data)
  {
    if (!empty($data[$this->primary_key])) {
      return $this->update($data);
    } else {
      return $this->insert($data);
    }
  }

  function get_by_id($id)
  {
    return $this->db->select("*")->get_where($this->table, [$this->primary_key => $id])->row_array();
  }


  function get_by_payment($payment, $only_get = true)
  {
    if ($only_get){
      return $this->db->where('pay_period_payment', $payment)
        ->order_by('pay_period_id', 'ASC')
        ->get($this->table)->result_array();
    }else{
      $this->db->select('*')
        ->join('client_periods', 'pay_period_clientperiod = clientperiod_id', 'left')
        ->join('client_packs', 'clientperiod_pack = clientpack_id', 'left')
        ->order_by('pay_period_id', 'ASC')
        ->where('pay_period_payment', $payment);
      return $this->db->get($this->table)->result_array();
    }
  }

  function get_by_clientPeriod_and_payment($clientPeriod, $payment)
  {
    return $this->db
      ->where('pay_period_clientperiod', $clientPeriod)
      ->where('pay_period_payment',$payment)
      ->get($this->table)->row_array();
  }
}
