<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Insert_persons_20231007192708 extends CI_Migration
{

  public function up()
  {

    $this->db->insert("persons", [
      "person_id" => null
    ]);
    $personId = $this->db->insert_id();

    $personHistory = [
      // "personhistory_from" => '',
      "personhistory_doc" => '39221669',
      // "personhistory_end" => '',
      "personhistory_person" => $personId,
      "personhistory_cuil" => '20392206583',
      "personhistory_firstname" => 'Juan Joséeeeee',
      "personhistory_lastname" => 'Bolanoooo',
      "personhistory_sex" => 'M',
      "personhistory_civil_status" => 'single',
      "personhistory_contact_info" => 'Esa te la debo',
      "personhistory_birth" => '1995-08-04',
    ];

    $this->db->insert("person_history", $personHistory);
  }

  public function down()
  {
  }
}
