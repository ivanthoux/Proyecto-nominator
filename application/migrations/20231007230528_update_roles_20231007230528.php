<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Update_roles_20231007230528 extends CI_Migration
{

  public function up()
  {
    $this->db->query("UPDATE roles SET role_default_url = 'persons/all';");
  }

  public function down()
  {
  }
}
