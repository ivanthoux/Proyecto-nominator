<?php

defined('BASEPATH') or exit('No direct script access allowed');

class PaymentDetail_model extends Base_model
{

	protected $table = 'payment_detail';
	protected $single_for_view = 'payment_detail';
	protected $primary_key = 'pay_detail_id';
	protected $timestamp = false;
	protected $column_order = ['pay_detail_payment', 'pay_detail_amount', 'pay_detail_type'];
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


	function get_by_payment($payment, $extended = false)
	{
		// if ($extended) {
		// 	$this->db->select("REPLACE(JSON_EXTRACT(pay_detail_extra_data, '$.pay_bank_cod'), '" . '"' . "', " . '""' . ") AS pay_detail_bank_cod")
		// 		->select("REPLACE(JSON_EXTRACT(pay_detail_extra_data, '$.pay_expiration_date'), '" . '"' . "', " . '""' . ") AS pay_detail_expiration_date")
		// 		->select("REPLACE(JSON_EXTRACT(pay_detail_extra_data, '$.pay_clearing'), '" . '"' . "', " . '""' . ") AS pay_detail_clearing")
		// 		->select("REPLACE(JSON_EXTRACT(pay_detail_extra_data, '$.pay_number'), '" . '"' . "', " . '""' . ") AS pay_detail_number")
		// 		->select("REPLACE(JSON_EXTRACT(pay_detail_extra_data, '$.pay_cuit'), '" . '"' . "', " . '""' . ") AS pay_detail_cuit");
		// }
		$result = $this->db->select('payment_detail.*')
			->where('pay_detail_payment', $payment)
			->order_by($this->primary_key, 'ASC')
			->get($this->table);
		if (empty($result)) return [];
		$paymentDetails = $result->result_array();
		if ($extended) {
			foreach ($paymentDetails as $key => $paymentDetail) {
				$payDetailExtraData = $paymentDetail['pay_detail_extra_data'];
				$payDetailExtraDataJson = json_decode($payDetailExtraData, true);
				$paymentDetails[$key]['pay_bank_cod'] = !empty($payDetailExtraDataJson['pay_bank_cod']) ? $payDetailExtraDataJson['pay_bank_cod'] : '';
				$paymentDetails[$key]['pay_detail_expiration_date'] = !empty($payDetailExtraDataJson['pay_expiration_date']) ? $payDetailExtraDataJson['pay_expiration_date'] : '';
				$paymentDetails[$key]['pay_detail_clearing'] = !empty($payDetailExtraDataJson['pay_detail_extra_data']) ? $payDetailExtraDataJson['pay_detail_extra_data'] : '';
				$paymentDetails[$key]['pay_detail_number'] = !empty($payDetailExtraDataJson['pay_number']) ? $payDetailExtraDataJson['pay_number'] : '';
				$paymentDetails[$key]['pay_detail_cuit'] = !empty($payDetailExtraDataJson['pay_cuit']) ? $payDetailExtraDataJson['pay_cuit'] : '';
			}
		}
		return $paymentDetails;
	}
}
