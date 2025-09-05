<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_additionals_20231111214806 extends CI_Migration
{
  public function up()
  {
    $this->db->query("
      CREATE TABLE `additionals` (
        `additional_id` INT NOT NULL AUTO_INCREMENT, 
        `additional_order` INT DEFAULT 1, 
        `additional_name` varchar(300) NOT NULL,
        `additional_description` varchar(300) DEFAULT '',
        `additional_coefficient` float NOT NULL,
        `additional_remunerative` tinyint(1) NOT NULL DEFAULT '1',
        `additional_haber` tinyint(1) NOT NULL DEFAULT '1',
        `additional_key` varchar(255) CHARACTER SET latin1 NOT NULL,
        PRIMARY KEY (`additional_id`)
        ) ENGINE = InnoDB;
      ");
  }

  public function down()
  {
  }
}
