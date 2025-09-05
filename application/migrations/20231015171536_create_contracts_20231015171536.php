<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_contracts_20231015171536 extends CI_Migration
{
  public function up()
  {
    $this->db->query("
      CREATE TABLE `contracts` (
        `contract_id` INT NOT NULL AUTO_INCREMENT , 
        `contract_start` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `contract_end` datetime DEFAULT NULL,
        `contract_person` int(11) NOT NULL,
        `contract_union_agreement_title` varchar(100) DEFAULT 'Comercio ( CCT 130/1975 )',
        `contract_union_agreement_category` int(11) NOT NULL,
        `contract_mensual_hours` int(11) NOT NULL DEFAULT 192,
        `contract_hour_rate` float NOT NULL DEFAULT 9598.91,
        PRIMARY KEY (`contract_id`),
        KEY `contract_person` (`contract_person`),
        KEY `contract_union_agreement_category` (`contract_union_agreement_category`)
        ) ENGINE = InnoDB;
      ");
  }

  public function down()
  {
  }
}
