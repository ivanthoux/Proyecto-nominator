<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Movement_model extends Base_model
{

  protected $table = 'movements';
  protected $single_for_view = 'movimiento';
  protected $primary_key = 'id';
  protected $timestamp = false;
  protected $union_table = true;
  protected $column_order = ['info', 'date', 'amount', 'type', 'user', 'obs'];
  protected $column_search = ['client_firstname', 'client_lastname'];

  public function __construct()
  {
    parent::__construct();
    $this->datatable_customs_link = function ($row) {
      return $this->customLinks($row, $this->session->userdata('user_rol_label'));
    };
  }

  private function customLinks($row, $role)
  {
    $editLink = '&nbsp;';
    if (in_array($role, ['Super', 'Administrador'])) {
      if (empty($row['client_id'])) {
        if ($row['mov'] == "expenses") {
          $link = site_url('expenses/form/' . $row['id']);
        } else {
          $link = site_url('payments/form/' . $row['id']);
        }
      } else if (!empty($row['client_id'])) {
        $link = site_url('clientpayments/all/' . $row['client_id']);
      } else {
        $link = site_url('officeclosings/form/' . $row['officeclosing_id'] . '/' . $row[$this->primary_key]);
      }
      // $this->dd($row);
      if (!empty($link) && $row['presented'] == '0') {
        $editLink = '<a href="' . $link . '" class="btn btn-warning " title="Editar"><span class="fa fa-edit"></span></a>';
      } else {
        $editLink = 'Caja Cerrada';
      }
    }

    return $editLink;
  }

  public function amount_attribute($value, $row)
  {
    if ($row['export'] == 1)
      return $value;
    return '<span class="fa fa-' . ($row['mov'] == "expenses" ? 'hand-o-left' : 'hand-o-right') . '"></span> ' . money_formating($value);
  }

  public function user_attribute($value, $row)
  {
    if ($row['export'] == 1)
      return $value;
    return ($row['presented'] ? '<span class="fa fa-check"></span>' : '') . ' ' . $value;
  }

  public function date_attribute($value, $row)
  {
    if ($row['export'] == 1)
      return $value;
    return date('d-m-Y', strtotime($value));
  }

  public function mov_attribute($value, $row)
  {
    if ($row['export'] == 1)
      return $value;
    return ('<span class="fa fa-' . ($row['mov'] == "expenses" ? 'hand-o-left' : 'hand-o-right') . '"></span> ' . ($row['mov'] == "expenses" ? ' Salida' : ' Entrada'));
  }

  public function get_filters()
  {
    $filters = array('created_by');
    $options = array();
    foreach ($filters as $field) {
      if ($field == 'created_by' && (in_array($this->session->userdata('user_rol_label'), ['Super', 'Administrador', 'Propietario']) || can('view_all_movements'))) {
        $this->db->select('pay_created_by, user_firstname, user_lastname')
          ->join('users as u', 'user_id = pay_created_by')
          ->where('user_email <>', 'SYSTEM');

        $pay = $this->db->get_compiled_select('payments');

        $this->db->select('exp_created_by, user_firstname, user_lastname')
          ->join('users as u', 'user_id = exp_created_by')
          ->where('user_email <>', 'SYSTEM');

        $exp = $this->db->get_compiled_select('expenses');
        $temp = $this->db->query('SELECT DISTINCT * FROM (' . $pay . ' UNION ' . $exp . ') a order by user_firstname ASC')->result_array();
        foreach ($temp as $op) {
          $options[$field][] = array('title' => $op['user_firstname'] . ' ' . $op['user_lastname'], 'value' => $op['pay_created_by']);
        }
      }
    }
    // $this->dd($options);
    return $options;
  }

  function extend_datatable_query()
  {
    $this->column_order = ['info', 'date', 'amount', 'type', 'user', 'obs'];
    $this->column_search = ['info', 'obs'];

    $this->filter_union([]);
  }

  function balances($start = false, $end = false)
  {
    $get = $this->input->get();
    if (!empty($get['datefilter'])) {
      $dates = explode('/', $get['datefilter']);
      $start = date('Y-m-d 00:00:00', strtotime(trim($dates[0])));
      $end = date('Y-m-d 23:59:00', strtotime(trim($dates[1])));
      $this->db->where('pay_date >= "' . $start . '" AND pay_date <= "' . $end . '"');
    }
    $this->db->where('pay_voucher iS NULL', null, false);
    $this->filter_union(['start' => $start, 'end' => $end]);
    $this->db->select('SUM(CASE WHEN mov = "payments" THEN amount ELSE 0 END) as income');
    $this->db->select('SUM(CASE WHEN mov = "payments" AND type="Efectivo" THEN amount ELSE 0 END) as income_cash');
    // $this->db->select('SUM(CASE WHEN mov = "payments" AND type="Tarjeta" THEN amount ELSE 0 END) as income_card');
    $this->db->select('SUM(CASE WHEN mov = "payments" AND type="Cheque" THEN amount ELSE 0 END) as income_check');
    $this->db->select('SUM(CASE WHEN mov = "payments" AND (type = "Transferencia" OR type = "BANCO CORRIENTES - PAGOS.LINEA.nominator" OR type = "BANCO NACION - PAGOS.LINEA.NACION" OR type = "MERCADO PAGO - PAGOS.LINEA.MP" OR type = "BANCO MACRO - PAGOS.nominator.LINEA") THEN amount ELSE 0 END) as income_transfer');
    $this->db->select('SUM(CASE WHEN mov = "expenses" THEN amount ELSE 0 END) as outcome');
    $this->db->select('SUM(CASE WHEN mov = "expenses" AND type="Efectivo" THEN amount ELSE 0 END) as outcome_cash');
    return $this->db->get()->row_array();
  }

  function balance_not_closed($start = false, $end = false)
  {
    $get = $this->input->get();
    if (!empty($get['datefilter'])) {
      $dates = explode('/', $get['datefilter']);
      $start = date('Y-m-d 00:00:00', strtotime(trim($dates[0])));
      $end = date('Y-m-d 23:59:00', strtotime(trim($dates[1])));
      $this->db->where('pay_date >= "' . $start . '" AND pay_date <= "' . $end . '"');
    }
    $this->filter_union(['start' => $start, 'end' => $end, 'pay_presented' => 0, 'exp_presented' => 0]);
    $this->db->select('SUM(CASE WHEN mov = "payments" THEN amount ELSE 0 END) as income');
    $this->db->select('SUM(CASE WHEN mov = "payments" AND type="Efectivo" THEN amount ELSE 0 END) as income_cash');
    // $this->db->select('SUM(CASE WHEN mov = "payments" AND type="Tarjeta" THEN amount ELSE 0 END) as income_card');
    $this->db->select('SUM(CASE WHEN mov = "payments" AND type="Cheque" THEN amount ELSE 0 END) as income_check');
    $this->db->select('SUM(CASE WHEN mov = "payments" AND (type = "Transferencia" OR type = "BANCO CORRIENTES - PAGOS.LINEA.nominator" OR type = "BANCO NACION - PAGOS.LINEA.NACION" OR type = "MERCADO PAGO - PAGOS.LINEA.MP" OR type = "BANCO MACRO - PAGOS.nominator.LINEA") THEN amount ELSE 0 END) as income_transfer', false);
    $this->db->select('SUM(CASE WHEN mov = "payments" AND type="Cuenta Corriente" THEN 1 ELSE 0 END) as income_cc');
    $this->db->select('SUM(CASE WHEN mov = "expenses" THEN amount ELSE 0 END) as outcome');
    $this->db->select('SUM(CASE WHEN mov = "expenses" AND type="Efectivo" THEN amount ELSE 0 END) as outcome_cash');
    return $this->db->get()->row_array();
  }

  function filter_union($filter_data)
  {
    $get = $this->input->get();

    if (!empty($get['export'])) {
      $this->column_order = ['date', 'info', 'mov', 'amount', 'roadmap', 'type', 'user', 'presented', 'obs', 'export'];
    }

    if (empty($filter_data['status']) || $filter_data['status'] !== 'all') {
      $filter_data['status'] = empty($filter_data['status']) ? 'pending' : $filter_data['status'];
    }
    if (empty($filter_data['user_id'])) {
      $filter_data['user_id'] = $this->session->userdata('user_id');
    }
    /* subquery projection PAYMENTS */
    $this->db->from('payment_detail');
    //$this->db->select('pay_id AS id, "payments" AS mov, (pay_amount+pay_daytask) AS amount, pay_date AS date')
    $this->db->select('pay_id AS id, "payments" AS mov, pay_detail_amount AS amount, pay_date AS date')
      ->select((empty($get['export']) ? '0' : '1') . ' as export', false)
      ->select('case '
        . ' when c.client_id is not null OR c.client_id is not null then coalesce(c.client_id, cc.client_id)'
        . ' else null end AS client_id')
      ->select('case '
        . ' when c.client_id is not null then CONCAT(c.client_firstname, " ",c.client_lastname, " ", c.client_doc)'
        . ' else case '
        . ' when cc.client_id is not null then CONCAT(cc.client_firstname, " ",cc.client_lastname, " ", cc.client_doc)'
        . ' else "" end end AS info, '
        . ' clientpack_roadmap roadmap, '
        . ' pay_detail_type AS type, pay_created_by created_by, user_firstname AS user, pay_description as obs, pay_presented as presented', false);
    $this->db->join('payments', "pay_id = pay_detail_payment AND pay_detail_type <> 'Cuenta Corriente'", 'left')
      ->join('payment_clientperiods', 'pay_id = pay_period_payment', 'left')
      ->join('client_periods', 'clientperiod_id = pay_period_clientperiod', 'left')
      ->join('client_packs', 'clientpack_id = clientperiod_pack', 'left')
      ->join('clients c', 'c.client_id = clientpack_client', 'left')
      ->join('clients cc', 'cc.client_id = pay_client', 'left')
      ->group_by('pay_detail_id');
    $this->db->join('users as u', 'user_id = pay_created_by');

    if (!empty($filter_data['start']) && !empty($filter_data['end'])) {
      $this->db->where('pay_date >= "' . $filter_data['start'] . '" AND pay_date <= "' . $filter_data['end'] . '"');
    }

    if (!in_array($this->session->userdata('user_rol_label'), ['Super', 'Administrador', 'Propietario']) && !can('view_all_movements')) {
      $this->db->where('pay_created_by', $this->session->userdata('user_id'));
    }
    if (isset($filter_data['pay_presented'])) {
      $this->db->where('pay_presented', $filter_data['pay_presented']);
    }

    if (!empty($get['datefilter'])) {
      $dates = explode('/', $get['datefilter']);
      $start = date('Y-m-d 00:00:00', strtotime(trim($dates[0])));
      $end = date('Y-m-d 23:59:00', strtotime(trim($dates[1])));
      $this->db->where('pay_date >= "' . $start . '" AND pay_date <= "' . $end . '"');
    }
    if (!empty($get['type'])) {
      $this->db->where('pay_type', $get['type']);
    }
    $subquery_payments = $this->db->get_compiled_select();
    // $this->dd($subquery_payments);
    /* subquery projection PAYMENTS */

    /* subquery projection EXPENSES */
    $this->db->from('expenses');
    $this->db->select('exp_id AS id, "expenses" AS mov, exp_amount AS amount, exp_date AS date')
      ->select((empty($get['export']) ? '0' : '1') . ' as export', false)
      ->select('0, CONCAT(exp_category, CONCAT(" ",coalesce(exp_name, \'\'))) AS info, "", exp_type AS type, exp_created_by, user_firstname AS user, exp_description as obs, exp_presented as presented', false);
    $this->db->join('users as u', 'user_id = exp_created_by');

    if (!empty($start) && !empty($end)) {
      $this->db->where('exp_date >= "' . $start . '" AND exp_date <= "' . $end . '"');
    }
    if (!in_array($this->session->userdata('user_rol_label'), ['Super', 'Administrador', 'Propietario']) && !can('view_all_movements')) {
      $this->db->where('exp_created_by', $this->session->userdata('user_id'));
    }
    if (!empty($get['type'])) {
      $this->db->where('exp_type', $get['type']);
    }
    if (isset($filter_data['exp_presented'])) {
      $this->db->where('exp_presented', $filter_data['exp_presented']);
    }
    $subquery_expenses = $this->db->get_compiled_select();
    // $this->dd($subquery_expenses);
    /* subquery projection EXPENSES */

    $this->db->from('(( ' . $subquery_payments . ') UNION ALL (' . $subquery_expenses . ')) as movement', false);
    if (!empty($get['filter'])) {
      foreach ($get['filter'] as $filter => $val) {
        if ($filter == "pay_presented") {
          if (trim($val) == 0) {
            $this->db->where($filter, '0');
          } else {
            $this->db->where($filter . ' > ', '0');
          }
        } elseif (!empty($val)) {
          $this->db->where($filter, trim($val));
        }
      }
    }
    // $this->dd($this->db->get_compiled_select());
  }

  function close($start = false, $end = false, $id = 1)
  {
    $this->db->where('pay_created_by', $this->session->userdata('user_id'));

    if (!empty($start) && !empty($end)) {
      $this->db->where('pay_date >= "' . $start . '" AND pay_date <= "' . $end . '"');
    }
    $this->db->where('pay_presented', 0);
    $result = $this->db->update('payments', array('pay_presented' => $id, 'pay_presented_at' => date('Y-m-d G:i:s')));

    $this->db->where('exp_created_by', $this->session->userdata('user_id'));

    if (!empty($start) && !empty($end)) {
      $this->db->where('exp_created_at >= "' . $start . '" AND exp_created_at <= "' . $end . '"');
    }
    $this->db->where('exp_presented', 0);
    $result = $this->db->update('expenses', array('exp_presented' => $id, 'exp_presented_at' => date('Y-m-d G:i:s')));
  }

  function get_for_closing($filters)
  {
    $this->filter_union($filters);
    return $this->db->get()->result_array();
  }
}
