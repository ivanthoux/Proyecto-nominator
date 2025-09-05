<?php

defined('BASEPATH') or exit('No direct script access allowed');

class EmployeeEvaluation_model extends Base_model
{

  protected $table = 'employee_evaluations';
  protected $single_for_view = 'evaluación de empleado';
  protected $primary_key = 'employeeevaluation_id';
  protected $timestamp = false;
  protected $column_order = ['employeeevaluation_date', 'person_fullname', 'employeeevaluation_mean'];
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
    $link = "app.desactiveConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','manager/employeeevaluation_remove/" . $row[$this->primary_key] . "')";
    $remove_link = '<a class="btn btn-danger" onclick="' . $link . '" title="Baja"><span class="fa fa-ban"></span></a>';
    $pay_link = '';
    return '<a href="' . site_url('manager/employeeevaluation/' . $row['employeeevaluation_id']) . '" class="btn btn-primary" title="Editar cliente"><span class="fa fa-folder"></span></a>'
      . $pay_link
      . $edit_link
      . $remove_link;
  }
  function extend_datatable_query()
  {
    $this->db->select('employee_evaluations.*, person_history.*, CONCAT(CONCAT(personhistory_lastname,", "),personhistory_firstname) person_fullname, (employeeevaluation_productivity+employeeevaluation_attitude+employeeevaluation_workknowledge+employeeevaluation_cooperation+employeeevaluation_situationawareness+employeeevaluation_opentocriticism+employeeevaluation_creativity+employeeevaluation_accomplishmentcapacity+employeeevaluation_initiative)/9 as employeeevaluation_mean');
    $this->db->order_by('employeeevaluation_date', 'DESC');
    $this->db->join('person_history', 'personhistory_person = employeeevaluation_person');
    $this->db->where('NOW() > personhistory_from AND (NOW() < personhistory_end OR personhistory_end IS NULL)');
  }
  public function employeeevaluation_date_attribute($value, $row)
  {
    return '<span title="' . date('d/m/Y G:i', strtotime($value)) . '">' . date('d/m/Y', strtotime($value)) . '<span>';
  }

  private function translateNumber($number)
  {
    $translatedTypes = [
      1 => 'Insuficiente',
      2 => 'Deficiente',
      3 => 'Regular',
      4 => 'Bueno',
      5 => 'Óptimo',
    ];
    return $translatedTypes[$number] ?? "N/A";
  }
  public function employeeevaluation_productivity_attribute($value, $row)
  {
    return $this->translateNumber($value);
  }
  public function employeeevaluation_attitude_attribute($value, $row)
  {
    return $this->translateNumber($value);
  }
  public function employeeevaluation_workknowledge_attribute($value, $row)
  {
    return $this->translateNumber($value);
  }
  public function employeeevaluation_cooperation_attribute($value, $row)
  {
    return $this->translateNumber($value);
  }
  public function employeeevaluation_situationawareness_attribute($value, $row)
  {
    return $this->translateNumber($value);
  }
  public function employeeevaluation_opentocriticism_attribute($value, $row)
  {
    return $this->translateNumber($value);
  }
  public function employeeevaluation_creativity_attribute($value, $row)
  {
    return $this->translateNumber($value);
  }
  public function employeeevaluation_accomplishmentcapacity_attribute($value, $row)
  {
    return $this->translateNumber($value);
  }
  public function employeeevaluation_initiative_attribute($value, $row)
  {
    return $this->translateNumber($value);
  }
}
