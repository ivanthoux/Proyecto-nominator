<?php

defined('BASEPATH') or exit('No direct script access allowed');

class NotificationDetails_model extends Base_model
{

	protected $table = 'notification_details';
	protected $single_for_view = 'notification_details';
	protected $primary_key = 'notificationdetail_id';
	protected $timestamp = false;
	protected $column_order = ['notification_detail_payment', 'notification_detail_amount', 'notification_detail_type'];
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


	function getByNotification($notificationId)
	{
		$result = $this->db->select('notification_details.*')
			->where('notificationdetail_notification', $notificationId)
			->order_by($this->primary_key, 'ASC')
			->get($this->table);
		return !empty($result) ? $result->result_array() : [];
	}

	public function createNotificationDetail($notificationId, $observation, $extraData = null)
	{
		$this->load->model('NotificationDetails_model');
		$notificationDetails_model = new NotificationDetails_model();
		$notificationDetail = [
			"notificationdetail_notification" => $notificationId,
			"notificationdetail_observation" => $observation,
			"notificationdetail_extra_data" => $extraData,
		];
		$notificationDetailId = $notificationDetails_model->save($notificationDetail);
		return $notificationDetailId;
	}
}
