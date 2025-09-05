<?php

class Scripts extends CI_Controller
{
  protected $_SEPSA_PAGOFACIL = 90062721;

  public function __construct()
  {
    parent::__construct();
  }

  public function infoPHP()
  {
    phpinfo();
  }

  public function dd($object, $continue = false)
  {
    echo '<pre>' . print_r($object, true) . '</pre>';
    if (!$continue)
      die();
  }

  public function getCities($region_id)
  {
    $this->load->model(['Cities_model']);
    $cities_model = new Cities_model();
    $cities = $cities_model->getByRegion($region_id);
    echo json_encode(['status' => 'success', 'data' => $cities]);
  }

  public function getCategories()
  {
    $this->load->model(['UnionAgreementCategories_Model']);
    $unionAgreementCategoriesModel = new UnionAgreementCategories_Model();
    $get = $this->input->get();
    $unionAgreementType = $get['unionAgreementType'];
    $data = $unionAgreementCategoriesModel->getCategoriesByType($unionAgreementType);
    echo json_encode(['status' => 'success', 'data' => $data]);
  }

  public function getWorkingDays()
  {
    $get = $this->input->get();
    $startDateString = $get['startDateString'];

    $date = strtotime($startDateString);
    // Last date of current month. 
    $lastdate = strtotime(date("Y-m-t", $date));
    $startDateString = date("Y-m-d", $date);
    // Day of the last date  
    $endingDay = date("d", $lastdate);
    $endingMonth = date("m", $lastdate);
    $endingYear = date("Y", $lastdate);
    $lastdate = "$endingYear-$endingMonth-$endingDay";
    $data = $this->db->query("SELECT ((DATEDIFF('" . $lastdate . "', '" . $startDateString . "')) -
    ((WEEK('" . $lastdate . "') - WEEK('" . $startDateString . "')) * 2) -
    (CASE WHEN WEEKDAY('" . $lastdate . "') = 6 THEN 1 ELSE 0 END) -
    (CASE WHEN WEEKDAY('" . $startDateString . "') = 5 THEN 1 ELSE 0 END)) AS DifD ")->result_array();
    echo json_encode(['status' => 'success', 'data' => $data]);
  }
}
