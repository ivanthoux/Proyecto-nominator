<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_person_history_20231007184246 extends CI_Migration
{
  public function up()
  {
    $this->db->query("
      CREATE TABLE `person_history` (
        `personhistory_id` INT NOT NULL AUTO_INCREMENT , 
        `personhistory_from` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `personhistory_end` datetime DEFAULT NULL,
        `personhistory_doc` varchar(10) NOT NULL,
        `personhistory_person` int(11) NOT NULL,
        `personhistory_cuil` varchar(12) DEFAULT NULL,
        `personhistory_cvu` varchar(100) DEFAULT NULL,
        `personhistory_children` int DEFAULT 0,
        `personhistory_disabled_children` int DEFAULT 0,
        `personhistory_firstname` varchar(300) NOT NULL,
        `personhistory_lastname` varchar(300) NOT NULL,
        `personhistory_sex` varchar(1) NOT NULL,
        `personhistory_civil_status` ENUM('single', 'married', 'divorced', 'widow') NOT NULL DEFAULT 'single',
        `personhistory_contact_info` text DEFAULT '',
        `personhistory_birth` date NOT NULL,
        `personhistory_phone` varchar(20),
        `personhistory_mobile` varchar(20),
        `personhistory_address` varchar(500),
        `personhistory_email` varchar(100),
        PRIMARY KEY (`personhistory_id`),
        KEY `personhistory_person` (`personhistory_person`)
        ) ENGINE = InnoDB;
      ");
  }

  public function down()
  {
  }
}
