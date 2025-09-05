<?php

defined('BASEPATH') or exit('No direct script access allowed');

class UnionAgreementCategories_Model extends Base_model
{

  protected $table = 'union_agreement_categories';
  protected $single_for_view = 'categoría de convenio colectivo';
  protected $primary_key = 'unionagreementcategory_id';
  protected $timestamp = false;
  protected $column_order = ['unionagreementcategory_type', 'unionagreementcategory_category'];
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
    return '';
  }
  function extend_datatable_query()
  {
    $this->db->select('union_agreement_categories.*');
    $this->db->order_by('holiday_date', 'DESC');
  }

  public function getCategoriesByType($unionAgreementType){
    $this->db->select('union_agreement_categories.*');
    $this->db->where("unionagreementcategory_type", $unionAgreementType);
    $data = $this->db->get($this->table)->result_array();
    return $data;
  }

}
