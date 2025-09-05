<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ClientPack_model extends Base_model
{

  protected $table = 'client_packs';
  protected $single_for_view = 'packs de cliente';
  protected $primary_key = 'clientpack_id';
  protected $timestamp = false;
  protected $order = ['clientpack_start', 'desc'];
  protected $column_order = ['clientpack_title', 'clientpack_created_at', 'clientpack_start', 'clientpack_sessions', 'periods_not_paid'];
  protected $column_search = ['clientpack_title', 'clientpack_roadmap', 'client_doc', 'concat(client_lastname, \', \', client_firstname)'];

  public function __construct()
  {
    $this->column_order = ['clientpack_title', 'clientpack_created_at', 'clientpack_start', 'clientpack_sessions', 'periods_not_paid', 'clientpack_price', 'clientpack_final', 'user_creator', 'client_doc', 'client_name'];

    $this->datatable_customs_link = function ($row) {
      return $this->view_packages($row, $this->session->userdata('user_rol_label'));
    };
  }

  public function view_packages($row, $rol)
  {
    $edit_link = '';
    $remove_link = '';
    if ($row['client_active']) {
      $edit_link = empty($_GET['client']) ? '<a href="' . site_url('clientpacks/all/' . $row['clientpack_client']) . '" class="btn btn-warning " title="Ver"><span class="fa fa-eye"></span></a>' : '';
      // if (in_array($rol, ['Super', 'Administrador', 'Propietario'])) {
      if (in_array($rol, ['Super'])) {
        $edit_link = '<a href="' . site_url('clientpacks/form/' . $row['clientpack_client'] . '/' . $row[$this->primary_key]) . '" class="btn btn-warning " title="Ver"><span class="fa fa-eye"></span></a>';
        $link = "app.deleteConfirmAjax(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','" . site_url("clientpacks/remove/" . $row[$this->primary_key]) . "', [], '" . site_url('clientpacks/all/' . $row['clientpack_client']) . "')";
        $remove_link = '<a class="btn btn-danger" onclick="' . $link . '" title="Eliminar"><span class="fa fa-remove"></span></a>';
      }
    } else {
      $edit_link = '<a href="' . site_url('clientpacks/form/' . $row['clientpack_client'] . '/' . $row[$this->primary_key]) . '" class="btn btn-info" title="Ver"><span class="fa fa-eye"></span></a>';
    }

    $pagare = ''; //'<a href="' . site_url('clientpacks/receipt/' . $row['clientpack_client'] . '/' . $row[$this->primary_key]) . '" class="btn bg-olive" title="PAGARE"><span class="fa fa-book"></span></a>';
    $contrato = ''; //'<a href="' . site_url('clientpacks/contract/' . $row['clientpack_client'] . '/' . $row[$this->primary_key]) . '" class="btn bg-primary" title="CONTRATO"><span class="fa fa-book"></span></a>';
    $detalle = '<a href="' . site_url('clientpacks/receipt/' . $row['clientpack_client'] . '/' . $row[$this->primary_key] . '/periods') . '" class="btn bg-info" title="CUOTAS"><span class="fa fa-list"></span></a>';

    return (in_array($row['clientpack_state'], ['2', '6']) && (!empty($_GET['client']) || !in_array($rol, ['Super'])) ? $contrato . $pagare . $detalle : (in_array($row['clientpack_state'], ['1', '5']) && !empty($_GET['client']) ? $contrato . $pagare : '&nbsp;'))
      . ($row['clientpack_state'] != '3' || $rol == 'Super' ? $edit_link
        . $remove_link : '&nbsp;');
  }

  public function save_only($data)
  {
    if (!empty($data['clientpack_start'])) {
      $data['clientpack_start'] = date('Y-m-d G:i:s', strtotime($data['clientpack_start']));
    }
    if (!empty($data['clientpack_end'])) {
      $data['clientpack_end'] = date('Y-m-d G:i:s', strtotime($data['clientpack_end']));
    }
    if (isset($data['clientpack_oldpaid'])) {
      $clientpack_oldpaid = floatval($data['clientpack_oldpaid']);
      unset($data['clientpack_oldpaid']);
    }
    if (isset($data['clientbalance'])) {
      unset($data['clientbalance']);
    }
    return $this->update($data);
  }

  public function save($data, $client = false)
  {
    $periods = false;
    if (isset($data['periods'])) {
      $periods = true;
      unset($data['periods']);
    }
    if (!empty($data['clientpack_start'])) {
      $data['clientpack_start'] = date('Y-m-d G:i:s', strtotime($data['clientpack_start']));
    }
    if (!empty($data['clientpack_end'])) {
      $data['clientpack_end'] = date('Y-m-d G:i:s', strtotime($data['clientpack_end']));
    }

    $clientPackRules = array();

    if (!empty($data[$this->primary_key])) { //EDIT
      $starthour = date('G:i:00', strtotime($data['clientpack_start']));
      $old_pack = $this->get_by_id($data[$this->primary_key]);
      $data['clientpack_start'] = date('Y-m-d', strtotime($old_pack['clientpack_start'])) . ' ' . $starthour;

      foreach ($data as $key => $value) {
        if (strpos($key, 'packrule') !== false) {
          $rule = explode('_', $key);
          $clientPackRules[] = [
            'clientpackrule_clientpack' => $data[$this->primary_key],
            'clientpackrule_packrule' => $rule[1],
            'clientpackrule_value' => $value
          ];
          unset($data[$key]);
        }
      }

      $pack = $this->update($data);

      $this->db->where('clientpackrule_clientpack', $data[$this->primary_key])
        ->delete('client_pack_rules');
      foreach ($clientPackRules as $value) {
        $this->db->insert('client_pack_rules', $value);
      }

      if ($periods) {
        $periods = $this->db->where('clientperiod_pack', $data[$this->primary_key])->get('client_periods')->result_array();
        foreach ($periods as $p) {
          $p['clientperiod_date'] = date('Y-m-d ' . $starthour, strtotime($p['clientperiod_date']));
          $this->db->where('clientperiod_id', $p['clientperiod_id']);
          $this->db->update('client_periods', $p);
        }
      }
    } else { //NEW
      $data['clientpack_sessions_left'] = $data['clientpack_sessions'];
      $data['clientpack_created_at'] = date('Y-m-d G:i:s');
      $data['clientpack_created_by'] = ($data['clientpack_created_by'] ? $data['clientpack_created_by'] : $this->session->userdata('user_id'));

      foreach ($data as $key => $value) {
        if (strpos($key, 'packrule') !== false) {
          $rule = explode('_', $key);
          $clientPackRules[] = [
            'clientpackrule_clientpack' => null,
            'clientpackrule_packrule' => $rule[1],
            'clientpackrule_value' => $value
          ];
          unset($data[$key]);
        }
      }

      $pack = $this->insert($data);

      foreach ($clientPackRules as $value) {
        $value['clientpackrule_clientpack'] = $pack;
        $this->db->insert('client_pack_rules', $value);
      }

      if ($periods) {
        $newperiod = array(
          'clientperiod_pack' => $pack,
          'clientperiod_amount' => $data['clientpack_sessions_price'], //valor de cuota que puede recibir pagos parciales
          'clientperiod_amountfull' => $data['clientpack_sessions_price'], //valor total de cuota
          'clientperiod_amountcapital' => round($data['clientpack_price'] / $data['clientpack_sessions'], 2),
          'clientperiod_amountcapitalfull' => round($data['clientpack_price'] / $data['clientpack_sessions'], 2),
          'clientperiod_amountinterest' => round(($data['clientpack_final'] - $data['clientpack_price']) / $data['clientpack_sessions'], 2),
          'clientperiod_amountinterestfull' => round(($data['clientpack_final'] - $data['clientpack_price']) / $data['clientpack_sessions'], 2),
          'clientperiod_amountinterest_2' => round(($data['clientpack_sessions_2_price'] - $data['clientpack_sessions_price']), 2),
          'clientperiod_amountinterestfull_2' => round(($data['clientpack_sessions_2_price'] - $data['clientpack_sessions_price']), 2),
        );
        $limit = 1;
        $daystart = strtotime($data['clientpack_start']);
        $daystart = strtotime($data['clientpack_type'] . ' day', $daystart);
        while ($limit <= $data['clientpack_sessions']) {
          $newperiod['clientperiod_date'] = date('Y-m-d G:i', $daystart);

          $daystart = strtotime(floor($data['clientpack_type'] / 2) . ' day', $daystart);
          $newperiod['clientperiod_date_2'] = date('Y-m-d G:i', $daystart);

          $newperiod['clientperiod_packperiod'] = $limit;
          $this->db->insert('client_periods', $newperiod);

          $daystart = strtotime($data['clientpack_type'] . ' day', $daystart);

          if (date('w', $daystart) == 0) {
            $daystart = strtotime('next day', $daystart);
          }

          $limit++;
        }
      }
    }
    return $pack;
  }

  public function clientpack_price_attribute($value, $row)
  {
    return money_formating($value, true);
  }

  public function clientpack_final_attribute($value, $row)
  {
    return money_formating($value, true);
  }

  public function clientpack_start_attribute($value, $row)
  {
    return '<span title="' . date('d/m/Y G:i', strtotime($value)) . '">' . date('d/m', strtotime($value)) . '<span>';
  }
  public function clientpack_created_at_attribute($value, $row)
  {
    return '<span title="' . date('d/m/Y G:i', strtotime($value)) . '">' . date('d/m', strtotime($value)) . '<span>';
  }

  public function clientpack_end_attribute($value, $row)
  {
    return date('d/m/Y', strtotime($value));
  }

  public function clientpack_sessions_attribute($value, $row)
  {
    return $row['clientpack_sessions'] . ' (' . $row['clientpack_type'] . ')';
  }

  public function clientpack_title_attribute($value, $row)
  {
    $rest = $this->periods_not_paid_calculate($row);
    $estado = '';
    switch ($row['clientpack_state']) {
      case 1:
      case 5:
        $estado = '[PENDIENTE]';
        break;
      case 3:
        $estado = '[RECHAZADO]';
        break;
      case 4:
        $estado = '[JUDICIALIZADO]';
        break;
      case 6:
        $estado = '[A DOCUMENTAR]';
        break;
    }
    return ($rest <= 0 ? '<span class="fa fa-check"></span> ' : '') . $row['clientpack_title'] . " " . $estado;
  }

  public function periods_not_paid_attribute($value, $row)
  {
    $value = $this->periods_not_paid_calculate($row);
    return money_formating($value, true) . '(' . $row['periods_not_paid'] . ')';
  }

  public function periods_not_paid_calculate($row)
  {
    if ($row['clientpack_type'] == "Mensual Interés") {
      return $row['clientpack_onlyinterest_balance'];
    } else {
      return $row['periods_not_paid_amount'];
    }
  }

  public function get_filters()
  {
    if (in_array($this->session->userdata('user_rol'), ['super', 'sell_for'])) {
      $filters = array('clientpack_package', 'clientpack_created_by', 'clientpack_state', 'clientpack_paystate');
    } else {
      $filters = array('clientpack_package', 'clientpack_created_by', 'clientpack_state');
    }
    $options = array();
    foreach ($filters as $field) {
      if ($field == 'clientpack_package') {
        $this->db->select("pack_name, concat((select min(packpoint_min_sessions) from pack_points where packpoint_pack = pack_id), '/', (select max(packpoint_max_sessions) from pack_points where packpoint_pack = pack_id)) pack_sessions, clientpack_package")
          ->group_by($field . ", concat((select min(packpoint_min_sessions) from pack_points where packpoint_pack = pack_id), '/', (select max(packpoint_max_sessions) from pack_points where packpoint_pack = pack_id))")
          ->order_by('pack_name', 'ASC');
        $this->db->join('packs as p', 'pack_id = clientpack_package', 'left');
        $temp = $this->db->get($this->table)->result_array();
        // $this->dd($temp);
        foreach ($temp as $op) {
          if (!empty($op[$field])) {
            $options[$field][] = array('title' => $op['pack_name'] . ' ' . $op['pack_sessions'], 'value' => $op[$field]);
          }
        }
      } else if ($field == 'clientpack_created_by') {

        $this->db->select('user_id, concat(CASE WHEN user_lastname <> "" THEN CONCAT(user_lastname, ", ") ELSE "" END, user_firstname) user_name')
          ->group_by('user_id, concat(CASE WHEN user_lastname <> "" THEN CONCAT(user_lastname, ", ") ELSE "" END, user_firstname)')
          ->order_by('concat(CASE WHEN user_lastname <> "" THEN CONCAT(user_lastname, ", ") ELSE "" END, user_firstname)', 'ASC', false)
          ->from('users')
          ->join('client_packs', 'clientpack_created_by = user_id');
        $temp = $this->db->get()->result_array();
        foreach ($temp as $op) {
          $options[$field][] = array('title' => $op['user_name'], 'value' => $op['user_id']);
        }
      } else if ($field == 'clientpack_state') {
        $options['clientpack_state'][] = array('title' => 'PENDIENTE', 'value' => 1);
        $options['clientpack_state'][] = array('title' => 'AUTORIZADO', 'value' => 2);
        $options['clientpack_state'][] = array('title' => 'RECHAZADO', 'value' => 3);
        $options['clientpack_state'][] = array('title' => 'EN ESTUDIO', 'value' => 4);
        $options['clientpack_state'][] = array('title' => 'PENDIENTE A DOCUMENTAR', 'value' => 5);
        $options['clientpack_state'][] = array('title' => 'A DOCUMENTAR', 'value' => 6);
      }
    }
    $options['clientpack_paystate'][] = array('title' => 'Con Mora', 'value' => 1);
    $options['clientpack_paystate'][] = array('title' => 'Con Cuotas Impagas', 'value' => 2);
    $options['clientpack_paystate'][] = array('title' => 'Con Cuotas Todas Pagas', 'value' => 3);
    return $options;
  }

  public function extend_datatable_query()
  {
    $get = $this->input->get();
    if (!empty($get['filter'])) {
      foreach ($get['filter'] as $filter => $val) {
        if ($filter == 'clientpack_paystate') {
          switch ($val) {
            case 1: //Con Mora
              $this->db->where('(SELECT COUNT(clientperiod_id) FROM client_periods WHERE clientperiod_pack = clientpack_id AND clientperiod_paid = false AND clientperiod_date < CURRENT_TIMESTAMP) >', 0);
              // $this->db->where('(SELECT COUNT(clientperiod_id) FROM client_periods WHERE clientperiod_pack = clientpack_id AND clientperiod_paid = false) >', 0);
              break;
            case 2: //Con cuotas impagas incluye con Mora
              $this->db->where('(SELECT COUNT(clientperiod_id) FROM client_periods WHERE clientperiod_pack = clientpack_id AND clientperiod_paid = false  AND clientperiod_date < CURRENT_TIMESTAMP) >=', 0);
              // $this->db->where('(SELECT COUNT(clientperiod_id) FROM client_packs JOIN client_periods on clientperiod_pack = clientpack_id where clientperiod_paid = false ) >', 0);
              break;
            case 3: // con cuotas TODAS pagas
              $this->db->where('(SELECT COUNT(clientperiod_id) FROM client_periods WHERE clientperiod_pack = clientpack_id AND clientperiod_paid = false) =', 0);
              break;
          }
        } else if (!empty($val)) {
          $this->db->where($filter, trim($val));
        }
      }
    }
    $this->db->select('client_packs.*');
    $this->db->select('CONCAT(CONCAT(user_firstname," "),user_lastname) AS user_creator');
    $this->db->select('client_doc, client_firstname client_name, client_active');
    $this->db->select('(SELECT ROUND(SUM(CASE WHEN clientperiod_paid = 0 THEN clientperiod_amount ELSE 0 END)) FROM client_periods WHERE clientperiod_pack = clientpack_id) as periods_not_paid_amount');
    $this->db->select('(SELECT SUM(CASE WHEN clientperiod_paid = 0 THEN 1 ELSE 0 END) FROM client_periods WHERE clientperiod_pack = clientpack_id) as periods_not_paid');
    $this->db->join('packs as p', 'pack_id = clientpack_package', 'left');
    $this->db->join('clients as c', 'client_id = clientpack_client');
    $this->db->join('users', 'user_id = clientpack_created_by');

    if (!empty($get['datefilter'])) {
      $dates = explode('/', $get['datefilter']);
      $start = date('Y-m-d 00:00:00', strtotime(trim($dates[0])));
      $end = date('Y-m-d 23:59:00', strtotime(trim($dates[1])));
      $this->db->where('clientpack_created_at >= "' . $start . '" AND clientpack_created_at <= "' . $end . '"');
    }

    if (!empty($get['client'])) {
      if (!empty($get['parent'])) {
        $this->db->where('clientpack_client = ' . $get['client'] . ' OR clientpack_client = ' . $get['parent'], false, false);
      } else {
        $this->db->where('clientpack_client = ' . $get['client'], false, false);
      }
    }
  }

  public function appoint()
  {
    $get = $this->input->get();
    $client = $this->db->get_where('clients', ['client_id' => $get['client']])->row_array();
    if (!empty($client['client_parent'])) {
      $_GET['parent'] = $client['client_parent'];
      $parent = $this->db->get_where('clients', ['client_id' => $client['client_parent']])->row_array();
      $this->db->select('*, IF(clientpack_client != ' . $get['client'] . ', "' . $parent['client_firstname'] . ' ' . $parent['client_lastname'] . '","") as attached', false);

      if (can("view_full_client")) {
        $this->db->select('IF(clientpack_client != ' . $get['client'] . ', "' . $parent['client_balance'] . '","' . $client['client_balance'] . '") as balance', false);
      }
    } else {
      $this->db->select('*');
      if (can("view_full_client")) {
        $this->db->select('"' . $client['client_balance'] . '" as balance');
      }
    }
    $this->db->order_by('clientpack_title', 'ASC');
    $this->extend_datatable_query();
    return $this->db->get($this->table)->result_array();
  }

  public function get_by_id($id, $only_get = false)
  {
    $this->db->select("client_packs.*");
    if (empty($only_get)) {
      $this->db->select("p.*, user_firstname, user_lastname");
      $this->db->select('(SELECT ROUND(SUM(CASE WHEN clientperiod_paid = 0 THEN clientperiod_amount ELSE 0 END)) FROM client_periods WHERE clientperiod_pack = clientpack_id) as periods_not_paid_amount');
      $this->db->select('(SELECT SUM(CASE WHEN clientperiod_paid = 0 THEN 1 ELSE 0 END) FROM client_periods WHERE clientperiod_pack = clientpack_id) as periods_not_paid');
      $this->db->join('packs as p', 'pack_id = clientpack_package', 'left');
      $this->db->join('users', 'clientpack_created_by = user_id', 'left');
    }
    $this->db->where('clientpack_id', $id);
    $data = $this->get('client_packs')->row_array();
    // if (date('G:i', strtotime($data['clientpack_start'])) == "0:00") {
    //     $periods = $this->db->where('clientperiod_pack', $data[$this->primary_key])->get('client_periods')->result_array();
    //     if (!empty($periods)) {
    //         $data['clientpack_start'] = date('Y-m-d', strtotime($data['clientpack_start'])) . ' ' . date('G:i:s', strtotime($periods[0]['clientperiod_date']));
    //     }
    // }

    return $data;
  }

  public function get_by_clientperiod_id($clientPeriodId)
  {
    $this->db->select("client_packs.*");
    $this->db->join("client_periods","clientperiod_pack = clientpack_id");
    $this->db->where('clientperiod_id', $clientPeriodId);
    $data = $this->get('client_packs')->row_array();
    return $data;
  }

  public function dashboard($start = false, $end = false)
  {
    $this->db->select('COUNT(*) as budget_operations');
    $this->db->select('SUM(clientpack_final) as budget_final');
    $this->db->select('SUM(clientpack_price) as budget_real');
    $this->db->select('SUM(clientpack_expenses) as budget_expenses');
    $this->db->select('SUM(CASE WHEN clientpack_expenses > 0 THEN 1 ELSE 0 END) as operations_expenses');
    $this->db->select('SUM(CASE WHEN clientpack_type = "Semanal" THEN clientpack_final ELSE 0 END) as budget_week');
    $this->db->select('SUM(CASE WHEN clientpack_type = "Mensual" THEN clientpack_final ELSE 0 END) as budget_month');
    $this->db->select('SUM(CASE WHEN clientpack_type = "Semanal" THEN 1 ELSE 0 END) as operations_week');
    $this->db->select('SUM(CASE WHEN clientpack_type = "Mensual" THEN 1 ELSE 0 END) as operations_month');

    if (!empty($start) && !empty($end)) {
      $this->db->where('clientpack_created_at between "' . $start . '" AND "' . $end . '"', false, false);
    }
    $data = $this->db->get($this->table)->row_array();

    return $data;
  }

  public function redifine_pack_monthinterest($client, $data)
  {
    $sessions = $data['clientpack_sessions'];
    $price = $data['clientpack_new_balance'];
    $commision = $data['clientpack_commision'];
    $newFinal = ($price * ($commision / 100 + 1)) + $data['clientpack_expenses'];
    $session_price = round((($price * ($commision / 100)) + $data['clientpack_expenses']) / $sessions * 100) / 100;

    //ACTUALIZANDO EL NUEVO BALANCE DEL CLIENTE
    if ($data['clientpack_onlyinterest_balance'] >= $newFinal) {
      $balance = $client['client_balance'] - ($data['clientpack_onlyinterest_balance'] - $newFinal);
      $this->db->where('client_id', $client['client_id'])->update('clients', array('client_balance' => $balance));
    } elseif ($data['clientpack_onlyinterest_balance'] < $newFinal) {
      $balance = $client['client_balance'] + ($newFinal - $data['clientpack_onlyinterest_balance']);
      $this->db->where('client_id', $client['client_id'])->update('clients', array('client_balance' => $balance));
    }
    //ACTUALIZANDO EL NUEVO BALANCE DEL CLIENTE
    //ACTUALIZANDO EL PRODUCTO CREDITO
    $data['clientpack_onlyinterest_balance'] = $newFinal;
    $data['clientpack_onlyinterest_session']++;
    unset($data['clientpack_new_balance']);
    // echo '<pre>';
    // print_r($data);
    // die();
    $this->save($data);
    //CREANDO PROXIMA CUOTA DE PAGO
    $newperiod = array(
      'clientperiod_client' => $data['clientpack_client'],
      'clientperiod_pack' => $data['clientpack_id'],
      'clientperiod_amount' => $session_price,
      'clientperiod_amountfull' => $data['clientpack_final'],
      'clientperiod_monthinterest' => 1,
    );
    $period = 60 * 60 * 24 * 7 * 30;
    $daystart = strtotime($data['clientpack_start']);
    $daystart += $period;
    if (date('w', $daystart) == 0) {
      $daystart += 60 * 60 * 24;
    }
    $newperiod['clientperiod_date'] = date('Y-m-d G:i', $daystart);
    $newperiod['clientperiod_packperiod'] = $data['clientpack_onlyinterest_session'];
    $this->db->insert('client_periods', $newperiod);
  }

  public function get_rules($_id)
  {
    return $this->db->where('clientpackrule_clientpack', $_id)
      ->get('client_pack_rules')->result_array();
  }

  public function getPendents($uss = false)
  {
    $this->db->select('clientpack_id, client_id, client_firstname, client_lastname, pack_name, clientpack_price, '
      . "case when (clientpack_state = 1 OR clientpack_state = 5) then 'PENDIENTE' "
      . " when clientpack_state = 2 then 'AUTORIZADO' "
      . " when clientpack_state = 3 then 'RECHAZADO' "
      . " when clientpack_state = 6 then 'A DOCUMENTAR' end clientpack_state, "
      . " clientpack_created_at, clientpack_audited")
      ->join('clients', 'clientpack_client = client_id')
      ->join('packs', 'clientpack_package = pack_id');

    if (empty($uss)) {
      $this->db->where('(clientpack_state = 1 OR clientpack_state = 5)', null, false);
    } else {
      $this->db->where('clientpack_state !=', 1)
        ->where('clientpack_created_by', $uss)
        ->where('clientpack_viewed is null', null, false)
        ->where('clientpack_audited is not null', null, false);
    }
    return $this->db->get($this->table)->result_array();
  }

  public function get_operations()
  {
    $get = $this->input->get();
    $this->db->select('sum(clientpack_price) capital, count(clientpack_id) capital_numbers');
    $this->db->select('sum(CASE WHEN clientpack_state = 2 THEN clientpack_price ELSE 0 END) as capital_liquid, SUM(CASE WHEN clientpack_state = 2 THEN 1 ELSE 0 END) as capital_numbers_liquid');
    $this->db->select('sum(CASE WHEN clientpack_state = 6 THEN clientpack_price ELSE 0 END) as capital_pending, SUM(CASE WHEN clientpack_state = 6 THEN 1 ELSE 0 END) as capital_numbers_pending');
    $this->db->from($this->table);
    if (!empty($get['filter'])) {
      foreach ($get['filter'] as $filter => $val) {
        if ($filter == 'clientpack_paystate') {
          switch ($val) {
            case 1: //Con Mora
              $this->db->where('(SELECT COUNT(clientperiod_id) FROM client_periods WHERE clientperiod_pack = clientpack_id AND clientperiod_paid = false AND clientperiod_date < CURRENT_TIMESTAMP) >', 0);
              break;
            case 2: //Con cuotas impagas incluye con Mora
              $this->db->where('(SELECT COUNT(clientperiod_id) FROM client_periods WHERE clientperiod_pack = clientpack_id AND clientperiod_paid = false  AND clientperiod_date < CURRENT_TIMESTAMP) >=', 0);
              $this->db->where('(SELECT COUNT(clientperiod_id) FROM client_periods WHERE clientperiod_pack = clientpack_id AND clientperiod_paid = false ) >', 0);
              break;
            case 3: // con cuotas TODAS pagas
              $this->db->where('(SELECT COUNT(clientperiod_id) FROM client_periods WHERE clientperiod_pack = clientpack_id AND clientperiod_paid = false) =', 0);
              break;
          }
        } else if (!empty($val)) {
          $this->db->where($filter, trim($val));
        }
      }
    }

    if (!empty($get['datefilter'])) {
      $dates = explode('/', $get['datefilter']);
      $start = date('Y-m-d 00:00:00', strtotime(trim($dates[0])));
      $end = date('Y-m-d 23:59:00', strtotime(trim($dates[1])));
      $this->db->where('clientpack_created_at >= "' . $start . '" AND clientpack_created_at <= "' . $end . '"');
    }
    return $this->db->get()->row_array();
  }

  public function isJudicialized($client_id)
  {
    return $this->db->where('clientpack_state', 4)
      ->where('clientpack_client', $client_id)
      ->get($this->table)->result_array();
  }

  public function simpleSave($data)
  {
    if (!empty($data[$this->primary_key])) {
      return $this->update($data);
    } else {
      return $this->insert($data);
    }
  }

  public function getCallCenter($date, $unpai = false, $defaulter = false, $client = false, $clientpack = false, $state = ['2', '6'])
  {
    $this->db->select('pack_id, clientpack_id, client_id, client_doc, client_lastname, client_firstname, client_phone, client_mobile, CONCAT(cities.name, ' - ', regions.name) city_name, client_address, client_city')
      ->select("(SELECT COUNT(*) 
                FROM client_periods 
                WHERE clientperiod_pack = clientpack_id 
                    AND COALESCE((SELECT  MAX(pay_date) 
                        FROM payments
                        WHERE pay_clientperiod = clientperiod_id
                        HAVING MAX(pay_date) >= clientperiod_date), clientperiod_date) < '" . $date . "'
                    AND clientperiod_paid = 0) AS cuotas_sin_pagar", false)
      ->select("(SELECT clientperiod_date
                        FROM client_periods
                        WHERE clientperiod_pack = clientpack_id
                            AND COALESCE((SELECT MAX(pay_date)
                                FROM payments
                                WHERE pay_clientperiod = clientperiod_id
                                HAVING MAX(pay_date) >= clientperiod_date), clientperiod_date) < '" . $date . "'
                            AND clientperiod_paid = 0 
                        ORDER BY clientperiod_date ASC
                        LIMIT 1) AS fecha_cuota_vencida", false)
      ->join('clients', 'clientpack_client = client_id')
      ->join('cities', 'cities.id = client_city', 'LEFT')
      ->join('regions', 'regions.id = cities.region_id', 'LEFT')
      ->join('packs', 'pack_id = clientpack_package');

    $this->db->where('clientpack_state in (' . join(', ', $state) . ')', null, false);

    if ($client) {
      $this->db->where('client_id', $client);
    }
    if ($clientpack) {
      $this->db->where('clientpack_id', $clientpack);
    }
    $table = '(' . $this->db->get_compiled_select($this->table) . ') xx';
    // $this->dd($table);

    if ($unpai)
      $this->db->where('cuotas_sin_pagar > 0', null, false);
    if ($defaulter)
      $this->db->where('fecha_cuota_vencida > 0', null, false);

    return $this->db->get($table)->result_array();
  }

  function getRoadmap($road, $limit = 8)
  {
    return $this->db->select('clientpack_roadmap')
      ->where('clientpack_roadmap like \'%' . $road . '%\'')
      ->order_by('clientpack_roadmap')
      ->group_by('clientpack_roadmap')
      ->limit($limit)
      ->get($this->table)->result_array();
  }

  function getClientsRoad($road, $client, $limit = 8)
  {
    return $this->db->select('client_doc, client_firstname')
      ->where('clientpack_roadmap like \'' . $road . '\'')
      ->where('concat(client_firstname, \' \', client_doc) like \'%' . $client . '%\'')
      ->join('clients', 'client_id = clientpack_client')
      ->order_by('client_firstname')
      ->group_by('client_doc, client_firstname')
      ->limit($limit)
      ->get($this->table)->result_array();
  }
}
