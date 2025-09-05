<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_persons_20231007183359 extends CI_Migration
{
  public function up()
  {
    $this->db->query("
      CREATE TABLE `persons` ( 
        `person_id` INT NOT NULL AUTO_INCREMENT , 
        PRIMARY KEY (`person_id`)
        ) ENGINE = InnoDB;
      ");
  }

  public function down()
  {
  }
}
