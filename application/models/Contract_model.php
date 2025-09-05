<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Contract_model extends Base_model
{

  protected $table = 'contracts';
  protected $single_for_view = 'contrato';
  protected $primary_key = 'contract_id';
  protected $timestamp = false;
  protected $column_order = ['person_fullname', 'contract_start', 'contract_end', 'contract_fullCategory'];
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
    $link = "app.desactiveConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','contracts/remove/" . $row[$this->primary_key] . "')";
    $remove_link = '<a class="btn btn-danger" onclick="' . $link . '" title="Baja"><span class="fa fa-ban"></span></a>';
    return '<a href="' . site_url('contracts/form/' . $row['contract_id']) . '" class="btn btn-primary" title="Editar contrato"><span class="fa fa-folder"></span></a>'
      . $remove_link;
  }
  function extend_datatable_query()
  {
    $this->db->select('contracts.*, person_history.*, CONCAT(CONCAT(personhistory_lastname,", "),personhistory_firstname) person_fullname');
    $this->db->select('CONCAT(CONCAT(unionagreementcategory_type," - "),unionagreementcategory_category) contract_fullCategory');
    $this->db->order_by('contract_start', 'DESC');
    $this->db->join('person_history', 'personhistory_person = contract_person');
    $this->db->join('union_agreement_categories', 'unionagreementcategory_id = contract_union_agreement_category');
    $this->db->where('NOW() > personhistory_from AND (NOW() < personhistory_end OR personhistory_end IS NULL)');
  }
  public function contract_start_attribute($value, $row)
  {
    return '<span title="' . date('d/m/Y G:i', strtotime($value)) . '">' . date('d/m/Y', strtotime($value)) . '<span>';
  }
  public function contract_end_attribute($value, $row)
  {
    return '<span title="' . date('d/m/Y G:i', strtotime($value)) . '">' . date('d/m/Y', strtotime($value)) . '<span>';
  }

  public function getByPersonAndDate($personId, $date)
  {
    $this->db->select('contracts.*')
      ->join("persons", "person_id = contract_person")
      ->where("person_id", $personId)
      ->where("contract_start <= '" . $date . "'", false, false)
      ->where("contract_end >= '" . $date . "'", false, false);
    $result = $this->db->get($this->table)->result_array();
    return count($result) > 0 ? $result[0] : [];
  }
}
