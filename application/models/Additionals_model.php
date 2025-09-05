<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Additionals_model extends Base_model
{

  protected $table = 'additionals';
  protected $single_for_view = 'adicional';
  protected $primary_key = 'additional_id';
  protected $timestamp = false;
  protected $column_order = ['additional_name', 'additional_description', 'additional_coefficient', 'additional_remunerative'];
  protected $column_search = [];

  public function __construct()
  {
    parent::__construct();
    $this->datatable_customs_link = function ($row) {
      return $this->customLinks($row, $this->session->userdata('user_rol_label'));
    };
  }
  function customLinks($row, $rol)
  {
    $edit_link = '';
    $link = "app.desactiveConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','manager/additional_remove/" . $row[$this->primary_key] . "')";
    $remove_link = '<a class="btn btn-danger" onclick="' . $link . '" title="Baja"><span class="fa fa-ban"></span></a>';
    $pay_link = '';
    return '<a href="' . site_url('manager/additional/' . $row['additional_id']) . '" class="btn btn-primary" title="Editar adicional"><span class="fa fa-folder"></span></a>'
      . $pay_link
      . $edit_link
      . $remove_link;
  }
  function extend_datatable_query()
  {
    $this->db->select('additionals.*');
    $this->db->order_by('additional_id', 'ASC');
  }
  public function additional_remunerative_attribute($value, $row)
  {
    $translatedTypes = [
      0 => 'No remunerativo',
      1 => 'Remunerativo',
    ];
    return $translatedTypes[$value] ?? $value;
  }

  public function getAdditionalByKey($additionalKey){
    $this->db->select('additionals.*');
    $this->db->where("additional_key", $additionalKey);
    $data = $this->db->get($this->table)->result_array();
    return count($data) > 0 ? $data[0] : [];
  }
}
