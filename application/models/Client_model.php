<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Client_model extends Base_model
{

    protected $table = 'clients';
    protected $single_for_view = 'cliente';
    protected $primary_key = 'client_id';
    protected $timestamp = false;
    protected $order = ['client_firstname', 'asc'];
    protected $column_order = ['client_doc', 'client_firstname', 'city_name', 'client_balance'];
    protected $column_search = ['client_doc', 'client_firstname', 'client_lastname', "CONCAT(cities.name, ' - ', regions.name)"];

    public function __construct()
    {
        parent::__construct();
        // if (can("view_full_client")) {
        //     $this->column_order = ['client_firstname', 'client_lastname', 'city_name', 'client_balance'];
        // }

        $this->datatable_customs_link = function ($row) {
            return $this->customLinks($row, $this->session->userdata('user_rol_label'));
        };
    }

    function get_balance_client($client, $only_get = false)
    {
        $this->db->select('SUM(CASE WHEN clientperiod_paid = 0 THEN clientperiod_amount ELSE 0 END) as periods_not_paid_amount');
        $this->db->select('SUM(CASE WHEN clientperiod_paid = 0 THEN 1 ELSE 0 END) as periods_not_paid');
        // $this->db->select('SUM(CASE WHEN clientperiod_paid = 0 AND clientperiod_monthinterest = 1 THEN clientperiod_amountfull ELSE 0 END) as periods_not_paid_interest');

        $data = $this->db->where('clientpack_client', $client)
            ->join('client_packs', 'clientpack_id = clientperiod_pack and clientpack_state = 2')
            ->get('client_periods')->row_array();
        // print_r($data);
        // $this->db->select('SUM(clientpack_onlyinterest_balance) as packs_interest');
        // $interest = $this->db->where(array('clientpack_client' => $client, 'clientpack_type' => 'Mensual Interés'))->get('client_packs')->row_array();

        // $this->dd($interest);
        $data['periods_not_paid_amount'] = $data['periods_not_paid_amount'];//+ $interest['packs_interest'];

        return $data;
    }

    function customLinks($row, $rol)
    {
        $edit_link = '';
        $remove_link = '';
        if ($row['client_active']) {
            $edit_link = '<a href="' . site_url('clients/form/' . $row[$this->primary_key]) . '" class="btn btn-warning " title="Editar"><span class="fa fa-edit"></span></a>';
            if ($rol == 'Super') {
                $link = "app.desactiveConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','clients/desactive/" . $row[$this->primary_key] . "')";
                $remove_link = '<a class="btn btn-danger" onclick="' . $link . '" title="Baja"><span class="fa fa-ban"></span></a>';
            }
        } else {
            $edit_link = '<a href="' . site_url('clients/form/' . $row[$this->primary_key]) . '" class="btn btn-info" title="Ver"><span class="fa fa-eye"></span></a>';
            if ($rol == 'Super') {
                $link = "app.activeConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','clients/active/" . $row[$this->primary_key] . "')";
                $remove_link = '<a class="btn bg-olive" onclick="' . $link . '" title="Re-Activar"><span class="fa fa-check"></span></a>';
            }
        }
        $pay_link = '';
        if ($row['periods_not_paid']) {
            $pay_link = '<a href="' . site_url('clientperiods/all/' . $row[$this->primary_key]) . '" class="btn bg-info" title="Cuotas"><span class="fa fa-list"></span></a>';
        }
        return '<a href="' . site_url('clientpacks/all/' . $row['client_id']) . '" class="btn btn-primary" title="Productos Cliente"><span class="fa fa-folder"></span></a>'
            . $pay_link
            . $edit_link
            . $remove_link;
    }

    public function get_filters($super = true)
    {
        $options['client_active'][] = array('title' => 'Activos', 'value' => 1);
        $options['client_active'][] = array('title' => 'De Baja', 'value' => 0);

        $options['client_state'][] = array('title' => 'Con Mora', 'value' => 1);
        $options['client_state'][] = array('title' => 'Con Cuotas Impagas', 'value' => 2);
        $options['client_state'][] = array('title' => 'Con Cuotas Todas Pagas', 'value' => 3);
        $options['client_state'][] = array('title' => 'Sin Productos', 'value' => 4);
        // }
        return $options;
    }

    public function get_by_id($id, $only_get = false)
    {
        $response = $this->db->select("*")->get_where($this->table, [$this->primary_key => $id])->row_array();
        if (!$only_get) {
            $response['clientbalance'] = $this->get_balance_client($id);
        }
        return $response;
    }

    public function get_by_parent($id)
    {
        $response = $this->db->select("*, DATE_FORMAT(client_created,'%d-%m-%Y') as client_created")->get_where($this->table, ['client_parent' => $id])->result_array();
        return $response;
    }

    function extend_datatable_query()
    {
        $get = $this->input->get();

        $this->db->select('clients.*');
        $this->db->select('(SELECT (SUM(CASE WHEN clientperiod_paid = 0 THEN clientperiod_amount ELSE 0 END)) FROM client_periods join client_packs on clientpack_id = clientperiod_pack WHERE clientpack_client = client_id) as periods_not_paid_amount');
        $this->db->select('(SELECT SUM(CASE WHEN clientperiod_paid = 0 THEN 1 ELSE 0 END) FROM client_periods join client_packs on clientpack_id = clientperiod_pack WHERE clientpack_client = client_id) as periods_not_paid');
        $this->db->select("CONCAT(cities.name, ' - ', regions.name) city_name");
        $this->db->join('cities', 'cities.id = client_city', 'LEFT');
        $this->db->join('regions', 'regions.id = cities.region_id', 'LEFT');
        if (!empty($get['filter'])) {
            foreach ($get['filter'] as $filter => $val) {
                if ($filter == 'client_office' && $val != '') {
                    $this->db->where('(SELECT COUNT(user_id) FROM client_packs JOIN users on user_id = clientpack_created_by WHERE user_office = ' . $val . ' and clientpack_client = client_id) >', 0);
                } else if ($filter == 'client_seller' && $val != '') {
                    $this->db->where('(SELECT COUNT(clientpack_id) FROM client_packs WHERE clientpack_created_by = ' . $val . ' and clientpack_client = client_id) >', 0);
                } else if ($filter == 'client_pack' && $val != '') {
                    $this->db->where('(SELECT COUNT(clientpack_id) FROM client_packs WHERE clientpack_package = ' . $val . ' and clientpack_client = client_id) >', 0);
                } else if ($filter == 'client_active' && $val != '') {
                    $this->db->where('client_active', ($val == 1 ? 1 : 0));
                } else if ($filter == 'client_state' && $val != '') {
                    switch ($val) {
                        case 1: //Con Mora
                            $this->db->where('(SELECT COUNT(clientperiod_id) FROM client_packs JOIN client_periods on clientperiod_pack = clientpack_id where clientperiod_paid = false and clientpack_client = client_id AND clientperiod_date < CURRENT_TIMESTAMP) >', 0);
                            $this->db->where('(SELECT COUNT(clientperiod_id) FROM client_packs JOIN client_periods on clientperiod_pack = clientpack_id where clientperiod_paid = false and clientpack_client = client_id) >', 0);
                            break;
                        case 2: //Con cuotas impagas incluye con Mora
                            $this->db->where('(SELECT COUNT(clientperiod_id) FROM client_packs JOIN client_periods on clientperiod_pack = clientpack_id where clientperiod_paid = false and clientpack_client = client_id AND clientperiod_date < CURRENT_TIMESTAMP) >=', 0);
                            $this->db->where('(SELECT COUNT(clientperiod_id) FROM client_packs JOIN client_periods on clientperiod_pack = clientpack_id where clientperiod_paid = false and clientpack_client = client_id) >', 0);
                            break;
                        case 3: // con cuotas TODAS pagas
                            $this->db->where('(SELECT COUNT(clientperiod_id) FROM client_packs JOIN client_periods on clientperiod_pack = clientpack_id where clientperiod_paid = false and clientpack_client = client_id) =', 0);
                            break;
                        case 4: //Sin producto
                            $this->db->where('(SELECT COUNT(clientpack_id) FROM client_packs WHERE clientpack_client = client_id) =', 0);
                            break;
                    }
                } elseif (!empty($val)) {
                    $this->db->where($filter, trim($val));
                }
            }
        }
        // $this->dd($this->db->get_compiled_select('clients'));
    }

    public function set_balance($client, $newvalue)
    {
        $client['client_balance'] = $newvalue;
        $client = $this->update($client);
    }

    public function add_balance($client, $value)
    {
        $client = $this->get_by_id($client['client_id'], true);
        // echo $client['client_balance'] . '+' . $value . '=' . round($client['client_balance'] + abs($value), 2) . '<br>';
        $client['client_balance'] = round($client['client_balance'] + abs($value), 2);
        //TODO: Eventualmente se va a tener que dejar registrado en una tabla estas transacciones
        $client = $this->update($client);
    }

    public function less_balance($client, $value)
    {
        $client = $this->get_by_id($client['client_id'], true);
        // echo $client['client_balance'] . '-' . $value . '=' . round($client['client_balance'] - abs($value), 2) . '<br>';
        $client['client_balance'] = round($client['client_balance'] - abs($value), 2);
        $client = $this->update($client);
    }

    public function client_balance_attribute($value, $row)
    {
        return money_formating($row['client_balance']) . ' (' . $row['periods_not_paid'] . ')';
    }

    public function get_all()
    {
        if ($this->session->userdata('office_loaded')) {
            $this->db->where('client_office', $this->session->userdata('office_loaded')['office_id']);
        }
        $this->db->order_by('client_firstname', 'ASC');
        return $this->db->get($this->table)->result_array();
    }

    public function dashboard($start, $end)
    {
        // $this->db->select('count(*) as qty, client_origin');
        // // $this->db->where('client_created >= "'.$start.'" AND client_created <= "'.$end.'"',false,false);
        // $this->db->group_by('client_origin')->order_by('qty', 'DESC');
        // //sacar fecha inicio cliente consulta anidada
        // return $this->db->get('clients')->result_array();
        return [];
    }

    public function getSearch($search)
    {
      $this->db->group_start();
      $this->db->or_where("client_doc like '%". $search. "%'");
      $this->db->or_where("client_cuil like '%". $search. "%'");
      $this->db->or_where("client_firstname like '%". $search. "%'");
      $this->db->group_end();
      // $this->dd($this->db->get_compiled_select($this->table));
      return $this->db->get($this->table)->result_array();
    }

    public function get_by_client_period($clientPeriodId)
    {
        $response = $this->db->select('clients.*')
        ->join('client_packs', 'clientpack_client = client_id')
        ->join('client_periods', 'clientperiod_pack = clientpack_id')
        ->where('clientperiod_id', $clientPeriodId)->get($this->table)->row_array();
        return $response;
    }
}
