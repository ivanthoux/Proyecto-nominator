<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_users_20231007211139 extends CI_Migration
{
  public function up()
  {
    $this->db->query("INSERT INTO users (
      user_email,
      user_password,
      user_role_id,
      user_firstname,
      user_phone,
      user_lastname,
      user_code,
      user_created,
      user_updated,
      user_cookie_hash,
      user_cookie_time,
      user_activation_hash,
      user_active,
      user_incomplete,
      user_verified,
      user_avatar,
      user_password_reset_hash,
      user_password_reset_time,
      user_office,
      user_officelocation,
      user_payment_type,
      user_payment_rate)
    VALUES (
      'admin@admin.com',
      '81dc9bdb52d04dc20036dbd8313ed055',
      '1',
      'Administrador',
      '',
      '',
      '',
      '2023-10-07',
      NULL,
      NULL,
      NULL,
      NULL,
      '1',
      '0',
      '0',
      'user_avatar',
      NULL,
      NULL,
      '1',
      NULL,
      NULL,
      NULL
    );");
  }

  public function down()
  {
  }
}
