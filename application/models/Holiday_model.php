<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Holiday_model extends Base_model
{

  protected $table = 'holidays';
  protected $single_for_view = 'feriado';
  protected $primary_key = 'holiday_id';
  protected $timestamp = false;
  protected $column_order = ['holiday_date', 'holiday_type', 'holiday_detail'];
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
    $link = "app.desactiveConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','manager/holiday_remove/" . $row[$this->primary_key] . "')";
    $remove_link = '<a class="btn btn-danger" onclick="' . $link . '" title="Baja"><span class="fa fa-ban"></span></a>';
    $pay_link = '';
    return '<a href="' . site_url('manager/holiday/' . $row['holiday_id']) . '" class="btn btn-primary" title="Editar cliente"><span class="fa fa-folder"></span></a>'
      . $pay_link
      . $edit_link
      . $remove_link;
  }
  function extend_datatable_query()
  {
    $this->db->select('holidays.*');
    $this->db->order_by('holiday_date', 'DESC');
  }
  public function holiday_date_attribute($value, $row)
  {
    return '<span title="' . date('d/m/Y G:i', strtotime($value)) . '">' . date('d/m/Y', strtotime($value)) . '<span>';
  }
  public function holiday_type_attribute($value, $row)
  {
    $translatedTypes = [
      'immovable' => 'Inamovible',
      'portable' => 'Transladable',
      'bridge' => 'Puente',
    ];
    return $translatedTypes[$value] ?? $value;
  }
}
