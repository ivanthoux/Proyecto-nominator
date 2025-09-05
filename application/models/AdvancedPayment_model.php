<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AdvancedPayment_model extends Base_model
{

    protected $table = 'advanced_payment';
    protected $single_for_view = 'Adelantos';
    protected $primary_key = 'advanced_pay_id';
    protected $timestamp = false;
    protected $order = [];
    protected $column_order = [];
    protected $column_search = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_payment($pay_id)
    {
      return $this->db->from($this->table)->where("advanced_pay_payment", $pay_id)->get()->row_array();
    }
}
