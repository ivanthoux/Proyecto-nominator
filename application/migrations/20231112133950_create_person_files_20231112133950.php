<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_person_files_20231112133950 extends CI_Migration
{
  public function up()
  {
    $this->db->query("
    CREATE TABLE `person_files` (
      `personfile_id` int(11) NOT NULL AUTO_INCREMENT,
      `personfile_person` int(11) NOT NULL,
      `personfile_file_type` ENUM('personal file', 'capacitations', 'incidents') NOT NULL DEFAULT 'personal file',
      `personfile_file` varchar(120) NOT NULL,
      `personfile_type` varchar(300) NOT NULL,
      `personfile_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`personfile_id`),
      KEY `personfile_person` (`personfile_person`),
      CONSTRAINT `person_files_ibfk_1` FOREIGN KEY (`personfile_person`) REFERENCES `persons` (`person_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
      ");
  }

  public function down()
  {
  }
}
