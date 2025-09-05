<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ClientPeriod_model extends Base_model
{

  protected $table = 'client_periods';
  protected $single_for_view = 'cuotas';
  protected $primary_key = 'clientperiod_id';
  protected $timestamp = false;
  protected $column_order = ['clientperiod_date', 'client_firstname', 'clientperiod_amount', 'clientperiod_amountinterestfull_2', 'clientperiod_packperiod', 'clientperiod_pack', 'clientperiod_paid'];
  protected $column_search = ['client_doc', 'client_firstname', 'client_lastname', 'clientperiod_date', 'clientpack_title'];

  public function __construct()
  {
    parent::__construct();

    $this->datatable_customs_link = function ($row) {
      $buttons = $this->form_payment($row);
      return $buttons;
    };
  }

  function form_payment($row)
  {
    if (!empty($row['clientperiod_paid'])) {
      if ($row['payments']) {
        return '<a href="' . site_url('clientperiods/receipt/' . $row['clientperiod_id']) . '/1" class="btn bg-olive" title="Ver comprobante"><span class="fa fa-book"></span></a>';
      }
      return '&nbsp;';
    } else {
      $receipt = '';
      if ($row['payments']) {
        $receipt =  '<a href="' . site_url('clientperiods/receipt/' . $row['clientperiod_id']) . '/1" class="btn bg-olive" title="Ver comprobante"><span class="fa fa-book"></span></a>';
      }
      if (!in_array($this->session->userdata('user_rol'), ["seller", "sell_for"])) {
        if (!empty($this->input->get('getpaid'))) {
          return $receipt . '<a href="' . site_url('clientpayments/form/' . $row['clientpack_client']) . '" class="btn bg-olive" title="Cobrar Cliente"><span class="fa fa-money"></span></a>';
        } else {
          if ($row['clientpack_state'] != 4 || $this->session->userdata('user_rol') == 'super') {
            return $receipt . '<a href="' . site_url('clientpayments/form/' . $row['clientpack_client'] . '/0/' . $row['clientperiod_id']) . '" class="btn btn-warning" title="Cobrar Cliente"><span class="fa fa-money"></span></a>';
          } else {
            return '&nbsp;';
          }
        }
      } else {
        return '&nbsp;';
      }
    }
  }

  public function save($data)
  {
    if (!empty($data[$this->primary_key])) {
      $client = $this->update($data);
    } else {
      $client = $this->insert($data);
    }
    return $client;
  }

  public function clientperiod_amount_attribute($value, $row)
  {
    return money_formating(($row['clientperiod_amount'] > 0 ? $row['clientperiod_amount'] : $row['clientperiod_amountfull']));
  }

  public function clientperiod_amountinterestfull_2_attribute($value, $row)
  {
    return money_formating((($row['clientperiod_amount'] > 0 ? $row['clientperiod_amount'] : $row['clientperiod_amountfull']) + $row['clientperiod_amountinterest_2']));
  }

  public function clientperiod_date_attribute($value, $row)
  {
    $col = '';
    if (!empty($row['clientperiod_visited']) && strtotime($row['clientperiod_visited']) > strtotime('today midnight') && empty($row['clientperiod_paid'])) {
      $col .= '<span class="fa fa-check"></span>';
    } elseif (strtotime($value) < strtotime('tomorrow midnight') && empty($row['clientperiod_paid'])) {
      $col .= '<span class="fa fa-bell"></span>';
    }
    $col .= '<span class="hidden-xs">' . date('d-m-Y', strtotime($value)) . ' </span> ';
    $col .= date('G:i', strtotime($value));
    return $col;
  }

  public function clientperiod_paid_attribute($value, $row)
  {
    return !empty($row['clientperiod_paid']) ? 'PAGADO' : ($row['payments'] ? 'PARCIAL' : ($row['clientpack_state'] == '4' ? 'JUDICIALIZADO' : ''));
  }

  public function clientperiod_pack_attribute($value, $row)
  {
    return !empty($row['clientperiod_pack']) ? ($row['clientpack_title']) : $row['clientperiod_pack'];
  }

  function get_by_id($id, $only_get = false)
  {
    if (empty($only_get)) {
      $this->db->join('client_packs as p', 'clientpack_id = clientperiod_pack');
      $this->db->join('clients as c', 'clientpack_client = client_id');
    }
    return $this->db->select("*")->get_where($this->table, [$this->primary_key => $id])->row_array();
  }

  public function get_filters($client = true)
  {
    $filters = array('clientperiod_pack', 'clientpack_client');
    $options = array();
    foreach ($filters as $field) {
      if ($field == 'clientpack_client' && $client) {
        $this->db->select('clientpack_client, client_firstname, client_lastname')->group_by($field)->order_by('c.client_firstname', 'ASC');
        $this->db->join('client_packs', 'clientpack_id = clientperiod_pack');
        $this->db->join('clients', 'client_id = clientpack_client');
        $temp = $this->db->get($this->table)->result_array();
        foreach ($temp as $op) {
          if (!empty($op[$field])) {
            $options[$field][] = array('title' => $op['client_firstname'] . ' ' . $op['client_lastname'], 'value' => $op[$field]);
          }
        }
      } else if ($field == 'clientperiod_pack' && $client) {
        $this->db->select('pack_name, pack_id')->group_by('pack_id')->order_by('pack_name', 'ASC');
        $this->db->join('client_packs', 'clientpack_id = clientperiod_pack');
        $this->db->join('packs as p', 'pack_id = clientpack_package');
        $temp = $this->db->get($this->table)->result_array();
        foreach ($temp as $op) {
          $options[$field][] = array('title' => $op['pack_name'], 'value' => $op['pack_id']);
        }
      }
    }
    $options['clientperiod_paid'][] = array('title' => 'Pagado', 'value' => 1);
    $options['clientperiod_paid'][] = array('title' => 'No Pagado', 'value' => 0);
    // echo '<pre>';
    // print_r($options);
    // die();
    return $options;
  }

  function extend_datatable_query()
  {
    $get = $this->input->get();
    // $this->dd($get);
    if (isset($get['getpaid']) && $get['getpaid'] == 1) {
      $this->column_order = ['clientperiod_date', 'client_doc', 'client_firstname', 'client_address', 'clientperiod_amount', 'clientperiod_packperiod', 'clientperiod_pack'];
      $this->column_search = ['client_doc', 'client_firstname', 'client_lastname', 'clientperiod_date', 'client_address', 'clientpack_title'];
    }
    // Need to review this with Carlos
    $this->db->select('client_periods.*, clients.*, (SELECT COUNT(pay_period_id) FROM payment_clientperiods
      INNER JOIN payments ON (pay_period_payment = pay_id)
      LEFT JOIN payment_detail ON pay_id = pay_detail_payment
      WHERE pay_period_clientperiod = clientperiod_id AND pay_detail_type <> \'Cuenta Corriente\') payments, clientpack_client, client_firstname, client_lastname, client_active, clientpack_id, clientpack_title, clientpack_sessions, clientpack_state');
    $this->db->join('client_packs', 'clientpack_id = clientperiod_pack and clientpack_state in (2,4,6)');
    $this->db->join('clients', 'client_id = clientpack_client');
    if (!empty($get['client'])) {
      $this->db->where('clientpack_client', $get['client']);
    }

    if (!empty($get['filter'])) {
      foreach ($get['filter'] as $filter => $val) {
        if ($filter == 'clientperiod_paid' && $val != '') {
          $this->db->where('clientperiod_paid', $val);
        } else if ($filter == 'search' && $val != '') {
          $this->db->group_start();
          foreach ($this->column_search as $search) {
            $this->db->or_like($search, $val);
          }
          $this->db->group_end();
        } else if ($filter == 'clientpack_roadmap' && $val != '') {
          $this->db->where("clientpack_roadmap LIKE '" . $val . "'");
        } else if ($filter == 'clientpack_client' && $val != '') {
          $this->db->where("concat(client_firstname, ' ', client_doc) = '" . urldecode($val) . "'");
        } else {
          if (!empty($val)) {
            $this->db->where($filter, trim($val));
          }
        }
      }
    }
    if (!empty($get['datefilter'])) {
      $dates = explode('/', $get['datefilter']);
      if (count($dates) > 1) {
        // $this->dd("este");
        $start = date('Y-m-d 00:00:00', strtotime(trim($dates[0])));
        $end = date('Y-m-d 23:59:59', strtotime(trim($dates[1])));
        $this->db->where('clientperiod_date >= "' . $start . '" AND clientperiod_date <= "' . $end . '"');
      } else {
        // $this->dd("no este");
        $end = date('Y-m-d 23:59:59', strtotime(trim($get['datefilter'])));
        $this->db->where('clientperiod_date <= "' . $end . '"');
      }
    }

    if (!empty($this->input->get('getpaid'))) {
      // $this->db->where('(clientperiod_visited IS NULL OR clientperiod_visited < "'.$start.'")');
      $this->db->where('clientperiod_paid', 0);
      // $this->db->where('clientperiod_date <= "'.$end.'"');
      // if ($this->session->userdata('user_rol_label') != 'Super') {
      //     $this->db->where('(clientperiod_visited IS NULL OR clientperiod_visited < "' . $start . '")');
      // }
    }
    // $this->dd($this->db->get_compiled_select('client_periods'));
  }

  function get_by_payment($payment)
  {
    return $this->db->where('clientperiod_payment', $payment)
      ->get($this->table)->row_array();
  }

  function get_client_payment($client, $product = false, $onlyperiod = false, $dateEnd = false)
  {
    $this->db->select('client_periods.*');
    if (!$onlyperiod) {
      $this->db->select('client_firstname, client_lastname, clientpack_title, clientpack_sessions');
      $this->db->join('client_packs as p', 'clientpack_id = clientperiod_pack and clientpack_state in (2,6)');
      $this->db->join('clients', 'client_id = clientpack_client');
    }
    if (!empty($client)) {
      if ($onlyperiod) {
        $this->db->join('client_packs', 'clientpack_id = clientperiod_pack');
      }
      $this->db->where('clientpack_client', $client);
    }
    if (!empty($product)) {
      $this->db->where('clientperiod_pack', $product);
    }
    $this->db->where('clientperiod_date <= "' . (!empty($dateEnd) ? $dateEnd : date('Y-m-d 00:00', strtotime('+ 1 day'))) . '"');
    $this->db->where('clientperiod_paid', 0);
    // $this->dd($this->db->get_compiled_select($this->table));

    return $this->db->get('client_periods')->result_array();
  }

  function get_product_periods($client, $product = false, $onlyperiod = false)
  {
    if (!$onlyperiod) {
      $this->db->select('client_periods.*, client_firstname, client_lastname, clientpack_title, clientpack_sessions');
      $this->db->join('client_packs as p', 'clientpack_id = clientperiod_pack');
      $this->db->join('clients as c', 'client_id = clientpack_client');
    }
    if (!empty($client)) {
      $this->db->where('clientpack_client', $client);
    }
    if (!empty($product)) {
      $this->db->where('clientperiod_pack', $product);
    }
    $this->db->order_by('clientperiod_date', 'ASC');
    return $this->db->get('client_periods')->result_array();
  }

  function get_client_unpaid($client = false, $clientpack_pack = false, $pack = false, $date = false, $state = ['2', '6'])
  {
    $this->db->select('clientperiod_id, clientperiod_pack, clientperiod_packperiod, clientperiod_amount, clientperiod_amountcapital, clientperiod_amountcapitalfull, clientperiod_amountinterest, clientperiod_amountinterest_2')
      ->select('coalesce((select max(pay_date) from payments join payment_clientperiods on pay_period_payment = pay_id where pay_period_clientperiod = clientperiod_id AND pay_date >= clientperiod_date), clientperiod_date) clientperiod_date, clientperiod_date_2')
      ->select('client_doc, client_firstname, client_lastname, clientpack_title, clientpack_sessions');
    $this->db->join('client_packs', 'clientpack_id = clientperiod_pack');
    $this->db->join('clients as c', 'client_id = clientpack_client');
    $this->db->where('clientpack_state in (' . join(', ', $state) . ')', null, false);
    if (!empty($client)) {
      $this->db->where('clientpack_client', $client);
    }
    if (!empty($clientpack_pack)) {
      $this->db->where('clientperiod_pack', $clientpack_pack);
    }
    if (!empty($pack)) {
      $this->db->join('packs', 'pack_id = clientpack_package');
      $this->db->where('pack_id', $pack);
    }
    $this->db->where('clientperiod_paid', 0);
    if (!empty($date))
      $this->db->where('clientperiod_date <= "' . $date . '"');

    $table = '(' . $this->db->get_compiled_select($this->table) . ') xx';

    $this->db->order_by('clientperiod_date');

    return $this->db->get($table)->result_array();
  }

  function get_client_unpaid_by_product($client)
  {
    $this->db->select('p.*,clientperiod_paid');

    $this->db->join('client_packs as p', 'clientpack_id = clientperiod_pack');
    $this->db->where('clientpack_client', $client);
    $this->db->where('clientperiod_paid', 0);
    $this->db->group_by('clientpack_id');
    $this->db->order_by('clientperiod_date', 'ASC');
    return $this->db->get('client_periods')->result_array();
  }

  public function simpleview($params)
  {
    $params['start'] = date('Y-m-d G:i:s', strtotime($params['start']));
    $params['end'] = date('Y-m-d G:i:s', strtotime($params['end']));
    $this->db->select("client_periods.*");
    $this->db->select("'#15243b' as backgroundColor");
    // $this->db->select("CONCAT(  CONCAT(CONCAT(CONCAT(client_firstname,' '),LEFT(client_lastname,1)),'. ') , COALESCE(clientpack_title,'')) as title");
    $this->db->select("CONCAT(CONCAT(CONCAT(client_firstname,' '),LEFT(client_lastname,1)),'. ') as title");
    $this->db->select("DATE_FORMAT(clientperiod_date,'%Y-%m-%dT%TZ') as start");

    $this->db->join('clients', 'clientpack_client = client_id', 'left');
    $this->db->join('client_packs', 'clientperiod_pack = clientpack_id', 'left');
    $this->db->where('clientperiod_date >= "' . $params['start'] . '" AND clientperiod_date <= "' . $params['end'] . '"');

    if (!empty($params['filter'])) {
      foreach ($params['filter'] as $filter => $val) {
        if (!empty($val)) {
          $this->db->where($filter, trim($val));
        }
      }
    }

    $response = $this->db->get($this->table)->result_array();

    foreach ($response as $event => $val) {
      // $response[$event]['editable'] = $val['appoint_used'] ? false : true;
      $response[$event]['editable'] = false;
      $response[$event]['borderColor'] = 'grey';
      date_default_timezone_set('UTC');
      $response[$event]['end'] = date('Y-m-d\TH:i:sO', strtotime('+ 30 minutes', strtotime($response[$event]['start'])));
      $response[$event]['backgroundColor'] = $response[$event]['backgroundColor'];
    }
    return $response;
  }

  function balances($start = false, $end = false)
  {
    $get = $this->input->get();
    if (!empty($get['filter'])) {
      foreach ($get['filter'] as $filter => $val) {
        if ($filter == 'clientperiod_paid' && $val != '') {
          $this->db->where('clientperiod_paid', $val);
        } else if ($filter == 'search' && $val != '') {
          $this->db->group_start();
          foreach ($this->column_search as $search) {
            $this->db->or_like($search, $val);
          }
          $this->db->group_end();
        } else if ($filter == 'clientpack_roadmap' && $val != '') {
          $this->db->where("clientpack_roadmap LIKE '" . $val . "'");
        } else if ($filter == 'clientpack_client' && $val != '') {
          $this->db->where("concat(client_firstname, ' ', client_doc) = '" . urldecode($val) . "'");
        } else {
          if (!empty($val)) {
            $this->db->where($filter, trim($val));
          }
        }
      }
    }
    $post = $this->input->post();
    if (!empty($post)) {
      // $this->dd($post);
      if (isset($post['search']) && $post['search']['value'] != '') {
        $this->db->group_start();
        foreach ($this->column_search as $search) {
          $this->db->or_like($search, $post['search']['value']);
        }
        $this->db->group_end();
      }
    }
    if ($start && $end) {
      $this->db->where('clientperiod_date >= "' . $start . '" AND clientperiod_date <= "' . $end . '"');
    } else {
      $this->db->where('clientperiod_date <= "' . $end . '"');
    }
    // if (!empty($this->input->get('getpaid'))) {
    //     // $this->db->where('(clientperiod_visited IS NULL OR clientperiod_visited < "'.$start.'")');
    //     $this->db->where('clientperiod_paid', 0);
    //     // $this->db->where('clientperiod_date <= "'.$end.'"');
    //     // if ($this->session->userdata('user_rol_label') != 'Super') {
    //     //     $this->db->where('(clientperiod_visited IS NULL OR clientperiod_visited < "' . $start . '")');
    //     // }
    // }
    $this->db->select('SUM(CASE WHEN clientperiod_paid = 0 THEN clientperiod_amount ELSE 0 END) as periods_not_paid');
    $this->db->select('SUM(CASE WHEN clientperiod_paid = 1 THEN clientperiod_amountfull ELSE 0 END) as periods_paid');
    // $this->db->select('SUM(CASE WHEN clientperiod_paid = 0 THEN clientperiod_amountfull ELSE 0 END) as periods_not_paid_interest');
    // $this->db->select('SUM(CASE WHEN clientperiod_paid = 1 AND clientperiod_monthinterest = 1 THEN clientperiod_amount ELSE 0 END) as periods_paid_interest');
    $this->db->join('client_packs', 'clientpack_id = clientperiod_pack and clientpack_state in (2,4,6)');
    $this->db->join('clients', 'client_id = clientpack_client');

    // $this->dd($this->db->get_compiled_select('client_periods'));
    $response = $this->db->get($this->table)->row_array();
    $response['periods_not_paid_f'] = money_formating($response['periods_not_paid']);
    $response['periods_paid_f'] = money_formating($response['periods_paid']);
    $response['periods_total'] = money_formating($response['periods_paid'] + $response['periods_not_paid']);
    // $response['periods_paid'] = $response['periods_paid'];
    return $response;
  }

  function delete_pack($pack)
  {
    $this->db->delete($this->table, ['clientperiod_pack' => $pack]);
  }
}
