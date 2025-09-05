<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_table_20231126144550 extends CI_Migration
{
  public function up()
  {
    $this->db->query("
    CREATE TABLE `paychecks` (
      `paycheck_id` int(11) NOT NULL AUTO_INCREMENT,
      `paycheck_person` int(11) NOT NULL,
      `paycheck_contract` int(11) NOT NULL,
      `paycheck_basic_amount` double NOT NULL,
      `paycheck_bruto` double NOT NULL,
      `paycheck_neto` double NOT NULL,
      `paycheck_basic_diary_amount` double NOT NULL,
      `paycheck_basic_days` double NOT NULL,
      `paycheck_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`paycheck_id`),
      KEY `paycheck_person` (`paycheck_person`),
      CONSTRAINT `pay_check_ibfk_1` FOREIGN KEY (`paycheck_person`) REFERENCES `persons` (`person_id`),
      KEY `paycheck_contract` (`paycheck_contract`),
      CONSTRAINT `pay_check_ibfk_2` FOREIGN KEY (`paycheck_contract`) REFERENCES `contracts` (`contract_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
      ");

    $this->db->query("
    CREATE TABLE `paycheck_additionals` (
      `paycheckadditional_id` int(11) NOT NULL AUTO_INCREMENT,
      `paycheckadditional_paycheck` int(11) NOT NULL,
      `paycheckadditional_additional` int(11) NOT NULL,
      `paycheckadditional_order` INT DEFAULT 1,
      `paycheckadditional_base` double NOT NULL,
      `paycheckadditional_units` double,
      `paycheckadditional_coefficient` double,
      `paycheckadditional_remunerative` INT default 1,
      `paycheckadditional_haber` INT default 0,
      PRIMARY KEY (`paycheckadditional_id`),
      KEY `paycheckadditional_paycheck` (`paycheckadditional_paycheck`),
      CONSTRAINT `paycheckadditional_ibfk_1` FOREIGN KEY (`paycheckadditional_paycheck`) REFERENCES `paychecks` (`paycheck_id`),
      KEY `paycheckadditional_additional` (`paycheckadditional_additional`),
      CONSTRAINT `paycheckadditional_ibfk_2` FOREIGN KEY (`paycheckadditional_additional`) REFERENCES `additionals` (`additional_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
      ");
  }

  public function down()
  {
  }
}
