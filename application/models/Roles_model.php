<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Roles_model extends Base_model {
    protected $table = 'roles';
    protected $primary_key = 'role_id';
    protected $timestamp = false;
    protected $column_order = ['role_key', 'role_name'];

    function __construct() {
        parent::__construct();

        $this->datatable_edit_link = function ($row) {
            return site_url('manager/role/' . $row['role_id']);
        };
    }


    function get_all() {

        $all = $this->db->get('roles')->result_array();

        return $all;

    }
    function get_all_admin() {

        $all = $this->db->where("(roles.role_key = 'super' OR roles.role_key = 'admin')" )->get('roles')->result_array();

        return $all;

    }

    function count_all() {
        $this->db->from('roles');

        return $this->db->count_all_results();
    }

    function manager_validation() {
        $config = array(
            array(
                'field' => 'role_key',
                'label' => 'Clave',
                'rules' => 'required'
            ),
            array(
                'field' => 'role_name',
                'label' => 'Nombre',
                'rules' => 'required'
            ),

        );

        return $config;
    }


    public function del($id)
    {
        $this->db->delete('role_permission', array('role_id' => $id));
        $this->db->delete('roles', array('role_id' => $id));
    }

}
