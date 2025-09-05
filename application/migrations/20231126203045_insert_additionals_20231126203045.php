<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Insert_additionals_20231126203045 extends CI_Migration
{

  public function up()
  {
    $additional = [
      'additional_order' => '0',
      'additional_name' => 'Antiguedad',
      'additional_description' => 'Adicional por antigüedad',
      'additional_coefficient' => '.01',
      'additional_haber' => '1',
      'additional_remunerative' => '1',
      'additional_key' => 'antiquity',
    ];
    $this->db->insert("additionals", $additional);
    $additional = [
      'additional_order' => '1',
      'additional_name' => 'Adicional por asistencia y puntualidad',
      'additional_description' => 'Presentismo',
      'additional_coefficient' => '.0833',
      'additional_haber' => '1',
      'additional_remunerative' => '1',
      'additional_key' => 'presentism',
    ];
    $this->db->insert("additionals", $additional);
    
    $additional = [
      'additional_order' => '2',
      'additional_name' => 'Jubilación - Ley 24.241',
      'additional_description' => 'Jubilación',
      'additional_coefficient' => '0.11',
      'additional_haber' => '0',
      'additional_remunerative' => '1',
      'additional_key' => 'retirement',
    ];
    $this->db->insert("additionals", $additional);

    $additional = [
      'additional_order' => '3',
      'additional_name' => 'Ley 19.032 - INSSJP',
      'additional_description' => 'Obra social jubilación',
      'additional_coefficient' => '0.03',
      'additional_haber' => '0',
      'additional_remunerative' => '1',
      'additional_key' => 'INSSJP',
    ];
    $this->db->insert("additionals", $additional);

    $additional = [
      'additional_order' => '4',
      'additional_name' => 'Obra social',
      'additional_description' => 'Obra social',
      'additional_coefficient' => '0.03',
      'additional_haber' => '0',
      'additional_remunerative' => '1',
      'additional_key' => 'healthcare',
    ];
    $this->db->insert("additionals", $additional);

    $additional = [
      'additional_order' => '5',
      'additional_name' => 'FAECyS - Art. 100 CCT 130/75',
      'additional_description' => 'Federación Argentina de empleados de comercio',
      'additional_coefficient' => '0.005',
      'additional_haber' => '0',
      'additional_remunerative' => '1',
      'additional_key' => 'FAECyS',
    ];
    $this->db->insert("additionals", $additional);

    $additional = [
      'additional_order' => '6',
      'additional_name' => 'Sindicato - Art. 100 CCT 130/75',
      'additional_description' => 'Aportes al sindicato',
      'additional_coefficient' => '0.02',
      'additional_haber' => '0',
      'additional_remunerative' => '1',
      'additional_key' => 'union',
    ];
    $this->db->insert("additionals", $additional);

    // Non remunerative

    $additional = [
      'additional_order' => '7',
      'additional_name' => 'Obra social',
      'additional_description' => 'Obra social',
      'additional_coefficient' => '0.03',
      'additional_haber' => '0',
      'additional_remunerative' => '0',
      'additional_key' => 'healthcare_non_remunerative',
    ];
    $this->db->insert("additionals", $additional);

    $additional = [
      'additional_order' => '8',
      'additional_name' => 'FAECyS - Art. 100 CCT 130/75',
      'additional_description' => 'Federación Argentina de empleados de comercio',
      'additional_coefficient' => '0.005',
      'additional_haber' => '0',
      'additional_remunerative' => '0',
      'additional_key' => 'FAECyS_non_remunerative',
    ];
    $this->db->insert("additionals", $additional);

    $additional = [
      'additional_order' => '9',
      'additional_name' => 'Sindicato - Art. 100 CCT 130/75',
      'additional_description' => 'Aportes al sindicato',
      'additional_coefficient' => '0.02',
      'additional_haber' => '0',
      'additional_remunerative' => '0',
      'additional_key' => 'union_non_remunerative',
    ];
    $this->db->insert("additionals", $additional);
  }

  public function down()
  {
  }
}
