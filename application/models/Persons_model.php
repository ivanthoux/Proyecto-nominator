<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Persons_model extends Base_model
{

    protected $table = 'persons';
    protected $single_for_view = 'persona';
    protected $primary_key = 'person_id';
    protected $timestamp = false;
    protected $order = ['personhistory_firstname', 'asc'];
    protected $column_order = ['personhistory_doc', 'personhistory_firstname', 'personhistory_lastname', 'personhistory_email'];
    // protected $column_order = ['personhistory_doc', 'personhistory_firstname', 'city_name'];
    protected $column_search = ['personhistory_doc', 'personhistory_firstname', 'personhistory_lastname'];
    // protected $column_search = ['person_doc', 'person_firstname', 'person_lastname', "CONCAT(cities.name, ' - ', regions.name)"];

    public function __construct()
    {
        parent::__construct();
        $this->datatable_customs_link = function ($row) {
            return $this->customLinks($row, $this->session->userdata('user_rol_label'));
        };
    }


    function customLinks($row, $rol)
    {
        $edit_link = '';
        $remove_link = '';
        $pay_link = '';
        // return '';
        return '<a href="' . site_url('persons/form/' . $row['person_id']) . '" class="btn btn-primary" title="Editar cliente"><span class="fa fa-folder"></span></a>'
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

    public function save($data)
    {
        $this->load->model(['Persons_model', 'PersonHistory_model']);
        $personHistoryModel = new PersonHistory_model();
        $itsNewPerson = empty($data['person_id']);
        if ($itsNewPerson) {
            $data['person_id'] = $this->insert(["person_id" => null]);
        }
        $personId = $data['person_id'];
        unset($data['person_id']);
        $data['personhistory_person'] = $personId;
        foreach ($data as $key => $value) {
            if (strpos($key, "person_") !== false) {
                $data[str_replace('person_', 'personhistory_', $key)] = $value;
                unset($data[$key]);
            }
        }
        $personHistoryRow = $personHistoryModel->get_where(["personhistory_end" => NULL, "personhistory_person" => $personId]);
        $personHasHistoryRow = !empty($personHistoryRow);
        if ($personHasHistoryRow) {
            $personHistoryRow = $personHistoryRow[0];
            $personHistoryId = $personHistoryRow['personhistory_id'];
            $end = date('c', strtotime('-1 second'));
            $personHistoryModel->save([
                'personhistory_id' => $personHistoryId,
                'personhistory_end' => $end
            ]);
        }
        $from = date('c');
        $data['personhistory_from'] = $from;
        return $personHistoryModel->save($data);
    }

    public function get_by_id($id)
    {
        $this->db->join("person_history", "personhistory_person = person_id", "LEFT");
        $this->db->where("personhistory_end IS NULL");
        $response = $this->db->select("*")->get_where($this->table, [$this->primary_key => $id])->row_array();
        foreach ($response as $key => $value) {
            if (strpos($key, "personhistory_") !== false) {
                if (strpos($key, "personhistory_id") !== false) continue;
                $response[str_replace('personhistory_', 'person_', $key)] = $value;
                unset($response[$key]);
            }
        }
        return $response;
    }

    public function get_by_doc($document)
    {
        $this->db->join("person_history", "personhistory_person = person_id", "LEFT");
        $this->db->where("personhistory_end IS NULL");
        $this->db->where("personhistory_doc", $document);
        $response = $this->db->select("*")->get_where($this->table, ["personhistory_doc" => $document])->row_array();
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

        $this->db->select('persons.*, person_history.*');
        $this->db->join('person_history', 'personhistory_person = person_id');
        $this->db->where('NOW() > personhistory_from AND (NOW() < personhistory_end OR personhistory_end IS NULL)');
        // $this->dd($this->db->get_compiled_select('clients'));
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

    // public function getSearch($search)
    // {
    //     $this->db->group_start();
    //     $this->db->or_where("client_doc like '%" . $search . "%'");
    //     $this->db->or_where("client_cuil like '%" . $search . "%'");
    //     $this->db->or_where("client_firstname like '%" . $search . "%'");
    //     $this->db->group_end();
    //     // $this->dd($this->db->get_compiled_select($this->table));
    //     return $this->db->get($this->table)->result_array();
    // }

}
