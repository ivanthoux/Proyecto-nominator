<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Delete_rows_20231007210428 extends CI_Migration
{
  public function up()
  {
    $this->db->query("DELETE FROM payment_detail");
    $this->db->query("DELETE FROM advanced_payment");
    $this->db->query("DELETE FROM payment_clientperiods");
    $this->db->query("DELETE FROM payments");
    $this->db->query("DELETE FROM client_periods");
    $this->db->query("DELETE FROM client_packs");
    $this->db->query("DELETE FROM user_actions");
    $this->db->query("DELETE FROM user_permission");
    $this->db->query("DELETE FROM users");
  }

  public function down()
  {
  }
}
