<?php

defined('BASEPATH') or exit('No direct script access allowed');

class PersonHistory_model extends Base_model
{

    protected $table = 'person_history';
    protected $single_for_view = 'histórico';
    protected $primary_key = 'personhistory_id';
    protected $timestamp = false;
    protected $order = ['personhistory_id', 'desc'];
    protected $column_order = ['personhistory_doc', 'personhistory_firstname', 'personhistory_lastname', 'personhistory_doc'];
    protected $column_search = ['personhistory_doc', 'personhistory_firstname', 'personhistory_lastname'];

    public function __construct()
    {
        parent::__construct();
        $this->datatable_customs_link = function ($row) {
            return $this->customLinks($row, $this->session->userdata('user_rol_label'));
        };
    }

    public function get_by_id($id)
    {
        $this->db->join("person_history", "personhistory_person = person_id", "LEFT");
        $this->db->where("personhistory_end IS NULL");
        $response = $this->db->select("*")->get_where($this->table, [$this->primary_key => $id])->row_array();
        foreach ($response as $key => $value) {
            if (strpos($key, "personhistory_") !== false) {
                $response[str_replace('personhistory_', 'person_', $key)] = $value;
                unset($response[$key]);
            }
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
        $this->db->select('persons.*, person_history.*');
        $this->db->join('person_history', 'personhistory_person = person_id');
        $this->db->where('NOW() > personhistory_from AND (NOW() < personhistory_end OR personhistory_end IS NULL)');
        // $this->dd($this->db->get_compiled_select('clients'));
    }
}
