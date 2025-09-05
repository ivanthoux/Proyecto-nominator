<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_employee_evaluation_20231112232621 extends CI_Migration
{
  public function up()
  {
    $this->db->query("
    CREATE TABLE `employee_evaluations` (
      `employeeevaluation_id` int(11) NOT NULL AUTO_INCREMENT,
      `employeeevaluation_person` int(11) NOT NULL,
      `employeeevaluation_productivity` tinyint(1) DEFAULT '1',
      `employeeevaluation_attitude` tinyint(1) DEFAULT '1',
      `employeeevaluation_workknowledge` tinyint(1) DEFAULT '1',
      `employeeevaluation_cooperation` tinyint(1) DEFAULT '1',
      `employeeevaluation_situationawareness` tinyint(1) DEFAULT '1',
      `employeeevaluation_opentocriticism` tinyint(1) DEFAULT '1',
      `employeeevaluation_creativity` tinyint(1) DEFAULT '1',
      `employeeevaluation_accomplishmentcapacity` tinyint(1) DEFAULT '1',
      `employeeevaluation_initiative` tinyint(1) DEFAULT '1',
      `employeeevaluation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`employeeevaluation_id`),
      KEY `employeeevaluation_person` (`employeeevaluation_person`),
      CONSTRAINT `employee_evaluation_ibfk_1` FOREIGN KEY (`employeeevaluation_person`) REFERENCES `persons` (`person_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
      ");
  }

  public function down()
  {
  }
}
