<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Drop_tables_20231007215738 extends CI_Migration
{
  public function up()
  {
    $this->db->query("SET SESSION FOREIGN_KEY_CHECKS=0");
    $this->db->query("DROP TABLE IF EXISTS payment_detail");
    $this->db->query("DROP TABLE IF EXISTS advanced_payment");
    $this->db->query("DROP TABLE IF EXISTS payment_clientperiods");
    $this->db->query("DROP TABLE IF EXISTS client_periods");
    $this->db->query("DROP TABLE IF EXISTS payments");
    $this->db->query("DROP TABLE IF EXISTS client_packs");
    $this->db->query("DROP TABLE IF EXISTS packs");
    $this->db->query("DROP TABLE IF EXISTS pack_discounts");
    $this->db->query("DROP TABLE IF EXISTS pack_points");
    $this->db->query("DROP TABLE IF EXISTS pack_rules");
    $this->db->query("DROP TABLE IF EXISTS mercado_pago");
    $this->db->query("DROP TABLE IF EXISTS imports");
    $this->db->query("DROP TABLE IF EXISTS export_details");
    $this->db->query("DROP TABLE IF EXISTS exports");
    $this->db->query("DROP TABLE IF EXISTS closings");
    $this->db->query("DROP TABLE IF EXISTS expenses");
    $this->db->query("DROP TABLE IF EXISTS client_pack_rules");
    $this->db->query("DROP TABLE IF EXISTS phone_validation_session");
    $this->db->query("DROP TABLE IF EXISTS phone_validations");
    $this->db->query("DROP TABLE IF EXISTS points");
    $this->db->query("SET SESSION FOREIGN_KEY_CHECKS=1");
  }

  public function down()
  {
  }
}
